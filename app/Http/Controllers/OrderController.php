<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Traits\ManagesCart;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    use ManagesCart;

    private FirebaseService $firebase;
    private float $defaultDeliveryFee;

    /**
     * Allowed order status flow used across customer and admin experiences.
     *
     * @var array<int, string>
     */
    private array $statusOptions = ['Pending', 'Confirmed', 'Shipped', 'Completed', 'Canceled'];

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->defaultDeliveryFee = (float)config('services.orders.delivery_fee', 0);
    }

    /**
     * Display the checkout page with summary.
     */
    public function checkout(): View|RedirectResponse
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Your cart is empty. Add products before checking out.',
            ]);
        }

        $cartForView = $this->formatCartForView($cart);
        $summary = $this->summarizeCart($cart);

        return view('main.checkout', [
            'cart'        => $cartForView,
            'summary'     => $summary,
            'deliveryFee' => $this->defaultDeliveryFee,
            'grandTotal'  => $summary['total'] + $this->defaultDeliveryFee,
        ]);
    }

    /**
     * Legacy entry point retained for compatibility.
     */
    public function submit(Request $request): RedirectResponse
    {
        return $this->process($request);
    }

    /**
     * Handle checkout submission, persist the order, and redirect to WhatsApp.
     */
    public function process(Request $request): RedirectResponse
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Your cart is empty. Add products before checking out.',
            ]);
        }

        $validated = $request->validate([
            'name'             => 'required|string|min:3|max:120',
            'phone'            => 'required|string|min:8|max:20',
            'address'          => 'required|string|min:5|max:255',
            'note'             => 'nullable|string|max:500',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|string',
            'items.*.name'     => 'required|string',
            'items.*.price'    => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'total'            => 'required|numeric|min:0',
        ]);

        $formattedItems = array_map(function (array $item) {
            $quantity = max(1, (int)($item['quantity'] ?? 1));
            $price = (float)($item['price'] ?? 0);

            return [
                'id'       => (string)($item['id'] ?? ''),
                'name'     => $item['name'] ?? 'Product',
                'price'    => round($price, 2),
                'quantity' => $quantity,
                'subtotal' => round($price * $quantity, 2),
            ];
        }, $validated['items']);

        $subtotal = array_reduce($formattedItems, function ($carry, $item) {
            return $carry + $item['subtotal'];
        }, 0.0);
        $deliveryFee = max(0, (float)$request->input('delivery_fee', $this->defaultDeliveryFee));
        $expectedTotal = $subtotal + $deliveryFee;

        if (abs($expectedTotal - (float)$validated['total']) > 0.5) {
            return back()->withErrors([
                'total' => 'The submitted total does not match the calculated amount.',
            ])->withInput();
        }

        $orderId = '#' . date('ymd') . rand(1000, 9999);
        $orderKey = 'order_' . date('ymdHis') . Str::upper(Str::random(4));
        $normalizedPhone = $this->normalizePhone($validated['phone']);

        $orderRecord = [
            'key'        => $orderKey,
            'id'         => $orderId,
            'name'       => $validated['name'],
            'phone'      => $normalizedPhone,
            'address'    => $validated['address'],
            'note'       => $validated['note'] ?? null,
            'items'      => $formattedItems,
            'subtotal'   => round($subtotal, 2),
            'delivery_fee' => round($deliveryFee, 2),
            'total'      => round($expectedTotal, 2),
            'status'     => 'Pending',
            'created_at' => now()->toDateTimeString(),
        ];

        $this->firebase->getRef("orders/{$orderKey}")->set($orderRecord);
        $this->forgetCart();
        session()->forget('cart');

        session([
            'latest_order_key'  => $orderKey,
            'latest_order_data' => $orderRecord,
        ]);

        return redirect()->route('thankyou');
    }

    /**
     * Display the thank-you page after successful checkout.
     */
    public function thankyou(): View
    {
        $order = session('latest_order_data');
        $orderKey = session('latest_order_key');

        if (!$order && $orderKey) {
            $order = $this->findOrder($orderKey);
        }

        return view('main.thankyou', [
            'order'    => $order,
            'orderKey' => $order['key'] ?? $orderKey,
        ]);
    }

    /**
     * Generate a downloadable PDF invoice for a specific order.
     */
    public function downloadInvoice(string $id): Response|RedirectResponse
    {
        $order = $this->findOrder($id) ?? $this->findOrderByPublicId($id);

        if (!$order) {
            return redirect()
                ->route('thankyou')
                ->with('invoice_error', 'We could not find that order to generate an invoice.');
        }

        $safeFileId = preg_replace('/[^A-Za-z0-9_-]/', '', $order['id'] ?? $order['key'] ?? $id);
        $fileName = sprintf('HydroNova_Invoice_%s.pdf', $safeFileId ?: 'order');

        $pdf = Pdf::loadView('main.invoice', [
            'order' => $order,
        ])->setPaper('a4');

        return $pdf->download($fileName);
    }

    /**
     * Admin: list all orders.
     */
    public function adminIndex(): View
    {
        $ordersSnapshot = $this->firebase->getRef('orders')->getValue() ?? [];
        $orders = $this->transformOrders($ordersSnapshot);

        return view('admin.orders.index', [
            'orders'        => $orders,
            'statusOptions' => $this->statusOptions,
        ]);
    }

    /**
     * Admin: show a single order.
     */
    public function adminShow(string $orderKey): View|RedirectResponse
    {
        $order = $this->findOrder($orderKey);

        if (!$order) {
            return redirect()->route('admin.orders.index')->withErrors(['order' => 'Order not found.']);
        }

        return view('admin.orders.show', [
            'order'         => $order,
            'statusOptions' => $this->statusOptions,
        ]);
    }

    /**
     * Admin: edit form for order status.
     */
    public function adminEdit(string $orderKey): View|RedirectResponse
    {
        $order = $this->findOrder($orderKey);

        if (!$order) {
            return redirect()->route('admin.orders.index')->withErrors(['order' => 'Order not found.']);
        }

        return view('admin.orders.edit', [
            'order'         => $order,
            'statusOptions' => $this->statusOptions,
        ]);
    }

    /**
     * Admin: update status.
     */
    public function adminUpdate(Request $request, string $orderKey): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', $this->statusOptions),
        ]);

        $orderRef = $this->firebase->getRef("orders/{$orderKey}");

        if (!$orderRef->getSnapshot()->exists()) {
            return redirect()->route('admin.orders.index')->withErrors(['order' => 'Order not found.']);
        }

        $orderRef->update(['status' => $request->status]);

        return redirect()
            ->route('admin.orders.show', $orderKey)
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Admin: delete order.
     */
    public function adminDestroy(string $orderKey): RedirectResponse
    {
        $orderRef = $this->firebase->getRef("orders/{$orderKey}");

        if ($orderRef->getSnapshot()->exists()) {
            $orderRef->remove();
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Normalize Lebanese phone numbers to international format.
     */
    private function normalizePhone(string $phone): string
    {
        $digitsOnly = preg_replace('/[^0-9]/', '', $phone) ?? '';
        $digitsOnly = ltrim($digitsOnly, '0');

        if (!str_starts_with($digitsOnly, '961')) {
            $digitsOnly = '961' . $digitsOnly;
        }

        return $digitsOnly;
    }

    /**
     * Convert Firebase snapshot to an ordered list.
     *
     * @param array<string, mixed> $ordersSnapshot
     * @return array<int, array<string, mixed>>
     */
    private function transformOrders(array $ordersSnapshot): array
    {
        $orders = [];

        foreach ($ordersSnapshot as $key => $order) {
            if (!is_array($order)) {
                continue;
            }

            $orders[] = array_merge($order, [
                'key' => $key,
            ]);
        }

        usort($orders, function ($a, $b) {
            return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        });

        return $orders;
    }

    /**
     * Retrieve a single order by Firebase key.
     *
     * @return array<string, mixed>|null
     */
    private function findOrder(string $orderKey): ?array
    {
        $snapshot = $this->firebase->getRef("orders/{$orderKey}")->getValue();

        if (!$snapshot || !is_array($snapshot)) {
            return null;
        }

        $snapshot['key'] = $orderKey;

        return $snapshot;
    }

    /**
     * Retrieve an order by its public ID (e.g., #1234).
     *
     * @return array<string, mixed>|null
     */
    private function findOrderByPublicId(string $orderId): ?array
    {
        $ordersSnapshot = $this->firebase->getRef('orders')->getValue();

        if (!$ordersSnapshot || !is_array($ordersSnapshot)) {
            return null;
        }

        foreach ($ordersSnapshot as $key => $order) {
            if (!is_array($order)) {
                continue;
            }

            if (($order['id'] ?? null) === $orderId || $key === $orderId) {
                $order['key'] = $key;
                return $order;
            }
        }

        return null;
    }
}
