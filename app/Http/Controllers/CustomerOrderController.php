<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function __construct(private FirebaseService $firebase)
    {
    }

    public function index(Request $request): View
    {
        $uid = $request->session()->get('customer.uid');
        $ordersSnapshot = $this->firebase->getRef('orders')->getValue() ?? [];
        $orders = [];

        foreach ($ordersSnapshot as $key => $order) {
            if (!is_array($order)) {
                continue;
            }
            if (($order['uid'] ?? null) !== $uid) {
                continue;
            }

            $order['key'] = $key;
            $orders[] = $order;
        }

        usort($orders, fn ($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

        return view('main.my-orders', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, string $order): View
    {
        $uid = $request->session()->get('customer.uid');
        $orderData = $this->firebase->getRef("orders/{$order}")->getValue();

        if (!$orderData || ($orderData['uid'] ?? null) !== $uid) {
            abort(403);
        }

        $orderData['key'] = $order;
        $items = $this->firebase->getRef("order_items/{$order}")->getValue() ?? ($orderData['items'] ?? []);
        $orderData['items'] = is_array($items) ? $items : [];

        return view('main.order-show', [
            'order' => $orderData,
        ]);
    }
}
