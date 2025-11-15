<?php

namespace App\Http\Controllers;

use App\Traits\ManagesCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    use ManagesCart;

    /**
     * Display the cart page with current items.
     */
    public function index(): View
    {
        $cart = $this->getCart();

        return view('main.cart', [
            'cart'    => $this->formatCartForView($cart),
            'summary' => $this->summarizeCart($cart),
        ]);
    }

    /**
     * Add a product to the session cart.
     */
    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|string|max:120',
            'name'       => 'required|string|max:255',
            'price'      => 'required|numeric|min:0',
            'quantity'   => 'nullable|integer|min:1',
            'image'      => 'nullable|string|max:2048',
        ]);

        $cart = $this->getCart();
        $productId = (string)$data['product_id'];

        $cart[$productId] = [
            'id'       => $productId,
            'name'     => $data['name'],
            'price'    => (float)$data['price'],
            'quantity' => ($cart[$productId]['quantity'] ?? 0) + (int)($data['quantity'] ?? 1),
            'image'    => $data['image'] ?? null,
        ];

        $this->storeCart($cart);

        return back()->with('success', 'Product added to cart.');
    }

    /**
     * Update a specific line item quantity.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|string',
            'quantity'   => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();
        $productId = (string)$data['product_id'];

        if (!isset($cart[$productId])) {
            return back()->withErrors(['cart' => 'Unable to locate the selected product in your cart.']);
        }

        $cart[$productId]['quantity'] = (int)$data['quantity'];
        $this->storeCart($cart);

        return back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|string',
        ]);

        $cart = $this->getCart();
        unset($cart[(string)$data['product_id']]);
        $this->storeCart($cart);

        return back()->with('success', 'Product removed from cart.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): RedirectResponse
    {
        $this->forgetCart();

        return back()->with('success', 'Cart cleared.');
    }
}

