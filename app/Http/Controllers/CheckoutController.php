<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Traits\ManagesCart;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController extends Controller
{
    use ManagesCart;

    private float $defaultDeliveryFee;

    public function __construct(private FirebaseService $firebase)
    {
        $this->defaultDeliveryFee = (float)config('services.orders.delivery_fee', 0);
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Your cart is empty. Add products before checking out.',
            ]);
        }

        $customer = $request->session()->get('customer');
        $profile = $this->firebase->getRef('customers/' . $customer['uid'])->getValue() ?? [];

        $cartForView = $this->formatCartForView($cart);
        $summary = $this->summarizeCart($cart);

        return view('main.checkout', [
            'cart'        => $cartForView,
            'summary'     => $summary,
            'deliveryFee' => $this->defaultDeliveryFee,
            'grandTotal'  => $summary['total'] + $this->defaultDeliveryFee,
            'profile'     => $profile,
            'customer'    => $customer,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $validated = $request->validate([
            'full_name'        => 'required|string|min:3|max:120',
            'phone'            => 'required|string|min:8|max:30',
            'address'          => 'required|string|min:5|max:255',
            'city'             => 'nullable|string|max:120',
            'note'             => 'nullable|string|max:500',
            'save_to_profile'  => 'sometimes|boolean',
        ]);

        $cartItems = $this->formatCartForView($cart);
        $summary = $this->summarizeCart($cart);
        $deliveryFee = $this->defaultDeliveryFee;
        $grandTotal = $summary['total'] + $deliveryFee;

        $orderKey = 'order_' . now()->format('YmdHis') . '_' . Str::upper(Str::random(4));
        $orderId = '#' . now()->format('ymd') . rand(1000, 9999);
        $createdAt = now()->toDateTimeString();
        $customer = $request->session()->get('customer');

        $orderData = [
            'key'         => $orderKey,
            'id'          => $orderId,
            'uid'         => $customer['uid'],
            'status'      => 'Pending',
            'full_name'   => $validated['full_name'],
            'email'       => $customer['email'] ?? null,
            'phone'       => $validated['phone'],
            'address'     => $validated['address'],
            'city'        => $validated['city'] ?? null,
            'note'        => $validated['note'] ?? null,
            'items'       => $cartItems,
            'subtotal'    => $summary['total'],
            'delivery_fee'=> $deliveryFee,
            'total'       => $grandTotal,
            'created_at'  => $createdAt,
        ];

        $this->firebase->getRef("orders/{$orderKey}")->set($orderData);
        $this->firebase->getRef("order_items/{$orderKey}")->set($cartItems);

        if ($request->boolean('save_to_profile')) {
            $this->firebase->getRef('customers/' . $customer['uid'])->update([
                'full_name' => $validated['full_name'],
                'phone'     => $validated['phone'],
                'address'   => $validated['address'],
                'city'      => $validated['city'] ?? null,
                'email'     => $customer['email'] ?? null,
            ]);
        }

        $this->forgetCart();
        session([
            'latest_order_key' => $orderKey,
        ]);

        return redirect()->route('thankyou');
    }

    public function thankyou(Request $request): View
    {
        $orderKey = session('latest_order_key');
        $order = null;

        if ($orderKey) {
            $order = $this->firebase->getRef("orders/{$orderKey}")->getValue();
            if (is_array($order)) {
                $order['key'] = $orderKey;
            }
        }

        return view('main.thankyou', [
            'order' => $order,
        ]);
    }

    public function downloadInvoice(string $order): Response|RedirectResponse
    {
        $orderData = $this->firebase->getRef("orders/{$order}")->getValue();
        $customer = session('customer');

        if (!$orderData || ($orderData['uid'] ?? null) !== ($customer['uid'] ?? null)) {
            abort(403);
        }

        $pdf = Pdf::loadView('main.invoice', [
            'order' => $orderData,
        ])->setPaper('a4');

        $fileName = sprintf('HydroNova_Invoice_%s.pdf', $orderData['id'] ?? $order);

        return $pdf->download($fileName);
    }
}
