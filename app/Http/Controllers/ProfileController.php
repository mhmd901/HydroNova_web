<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private FirebaseService $firebase)
    {
    }

    public function edit(Request $request): View
    {
        $customer = $request->session()->get('customer');
        $profile = $this->firebase->getRef('customers/' . $customer['uid'])->getValue() ?? [];

        return view('main.profile', [
            'customer' => $customer,
            'profile'  => $profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone'     => ['required', 'string', 'max:30'],
            'address'   => ['required', 'string', 'max:255'],
            'city'      => ['nullable', 'string', 'max:120'],
        ]);

        $customer = $request->session()->get('customer');
        $this->firebase->getRef('customers/' . $customer['uid'])->update([
            'full_name' => $data['full_name'],
            'phone'     => $data['phone'],
            'address'   => $data['address'],
            'city'      => $data['city'] ?? null,
            'email'     => $customer['email'] ?? null,
        ]);

        $request->session()->put('customer.full_name', $data['full_name']);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
