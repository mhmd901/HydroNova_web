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
                $orders[] = array_merge($order ?? [], [
                    '_key' => $key,
                    'id'   => $order['id'] ?? ('#' . strtoupper(substr($key, -6))),
                ]);
            }
        }

        return view('admin.orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Update the status of a specific order.
     */
    public function updateStatus(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Delivered,Canceled',
        ]);

        $orderRef = $this->firebase->getRef("orders/{$id}");
        if (!$orderRef->getSnapshot()->exists()) {
            return back()->with('error', 'Order not found.');
        }

        $orderRef->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Delete an order from Firebase.
     */
    public function destroy(string $id): RedirectResponse
    {
        $orderRef = $this->firebase->getRef("orders/{$id}");
        if (!$orderRef->getSnapshot()->exists()) {
            return back()->with('error', 'Order not found.');
        }

        $orderRef->remove();

        return back()->with('success', 'Order deleted successfully.');
    }
}

