<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\WebAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function __construct(private WebAuthService $authService)
    {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        if ($this->authService->findCustomerByEmail($data['email'])) {
            return back()->withErrors(['email' => 'An account with that email already exists.'])->withInput();
        }

        $customerRecord = $this->authService->createCustomer(
            $data['full_name'],
            $data['email'],
            $data['password']
        );

        $this->setSessionCustomer($customerRecord['uid'], $customerRecord);

        return redirect()->intended('/');
    }

    public function login(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $customer = $this->authService->findCustomerByEmail($request->input('email'));

        if (!$customer || !$this->authService->verifyPassword($customer, $request->input('password'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        $this->setSessionCustomer($customer['uid'], $customer);

        return redirect()->intended('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('customer');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function setSessionCustomer(string $uid, array $customer): void
    {
        session([
            'customer' => [
                'uid'       => $uid,
                'email'     => $customer['email'] ?? null,
                'full_name' => $customer['full_name'] ?? ($customer['name'] ?? null),
            ],
        ]);
    }
}
