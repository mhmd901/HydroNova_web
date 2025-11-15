<?php

namespace App\Traits;

trait ManagesCart
{
    /**
     * Retrieve current cart from the session.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function getCart(): array
    {
        return session('cart', []);
    }

    /**
     * Persist cart back to the session.
     *
     * @param  array<string, array<string, mixed>>  $cart
     */
    protected function storeCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /**
     * Remove cart from the session.
     */
    protected function forgetCart(): void
    {
        session()->forget('cart');
    }

    /**
     * Format cart items for UI consumption.
     *
     * @param  array<string, array<string, mixed>>  $cart
     * @return array<int, array<string, mixed>>
     */
    protected function formatCartForView(array $cart): array
    {
        return array_values(array_map(function ($item) {
            $quantity = (int)($item['quantity'] ?? 0);
            $price = (float)($item['price'] ?? 0);

            return [
                'id'       => (string)($item['id'] ?? ''),
                'name'     => $item['name'] ?? 'Product',
                'price'    => round($price, 2),
                'quantity' => max(1, $quantity),
                'image'    => $item['image'] ?? null,
                'subtotal' => round($price * max(1, $quantity), 2),
            ];
        }, $cart));
    }

    /**
     * Calculate summary totals for the current cart.
     *
     * @param  array<string, array<string, mixed>>  $cart
     */
    protected function summarizeCart(array $cart): array
    {
        $subtotal = 0;
        $count = 0;

        foreach ($cart as $item) {
            $quantity = (int)($item['quantity'] ?? 0);
            $price = (float)($item['price'] ?? 0);
            $subtotal += $quantity * $price;
            $count += $quantity;
        }

        return [
            'count'    => $count,
            'subtotal' => round($subtotal, 2),
            'total'    => round($subtotal, 2),
        ];
    }
}

