<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display the orders list pulled from Firebase.
     */
    public function index(): View
    {
        $snapshot = $this->firebase->getRef('orders')->getSnapshot();
        $ordersData = $snapshot->getValue() ?? [];

        $orders = [];
        if (is_array($ordersData)) {
            foreach ($ordersData as $key => $order) {
                $orders[] = (object)array_merge($order ?? [], [
                    '_key' => $key,
                    'id'   => $order['id'] ?? ('#' . strtoupper(substr($key, -6))),
                ]);
            }
        }

        return view('admin.orders.index', [
            'orders' => $orders,
            'statusOptions' => ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
        ]);
    }

    /**
     * Show a single order.
     */
    public function show(string $orderKey): View|RedirectResponse
    {
        $order = $this->firebase->getRef("orders/{$orderKey}")->getValue();

        if (!$order || !is_array($order)) {
            return redirect()->route('admin.orders.index')->withErrors(['order' => 'Order not found.']);
        }

        $order['_key'] = $orderKey;
        $order['items'] = $this->firebase->getRef("order_items/{$orderKey}")->getValue() ?? ($order['items'] ?? []);

        return view('admin.orders.show', [
            'order' => (object)$order,
            'statusOptions' => ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
        ]);
    }

    /**
     * Edit form.
     */
    public function edit(string $orderKey): View|RedirectResponse
    {
        $order = $this->firebase->getRef("orders/{$orderKey}")->getValue();

        if (!$order || !is_array($order)) {
            return redirect()->route('admin.orders.index')->withErrors(['order' => 'Order not found.']);
        }

        $order['_key'] = $orderKey;
        $order['items'] = $this->firebase->getRef("order_items/{$orderKey}")->getValue() ?? ($order['items'] ?? []);

        return view('admin.orders.edit', [
            'order' => (object)$order,
            'statusOptions' => ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
        ]);
    }

    /**
     * Update the status of a specific order.
     */
    public function update(Request $request, string $orderKey): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Processing,Shipped,Delivered,Cancelled',
        ]);

        $orderRef = $this->firebase->getRef("orders/{$orderKey}");

        if (!$orderRef->getSnapshot()->exists()) {
            return back()->withErrors(['order' => 'Order not found.']);
        }

        $orderRef->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Delete an order from Firebase.
     */
    public function destroy(string $orderKey): RedirectResponse
    {
        $orderRef = $this->firebase->getRef("orders/{$orderKey}");
        if (!$orderRef->getSnapshot()->exists()) {
            return back()->with('error', 'Order not found.');
        }

        $orderRef->remove();
        $this->firebase->getRef("order_items/{$orderKey}")->remove();

        return back()->with('success', 'Order deleted successfully.');
    }
}
