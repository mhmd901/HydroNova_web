<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function __construct(private FirebaseService $firebase)
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

        if ($this->findCustomerByEmail($data['email'])) {
            return back()->withErrors(['email' => 'An account with that email already exists.'])->withInput();
        }

        $uid = 'cust_' . Str::lower(Str::random(10));
        $customerRecord = [
            'uid'           => $uid,
            'full_name'     => $data['full_name'],
            'email'         => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'created_at'    => now()->toDateTimeString(),
        ];

        $this->firebase->getRef("customers/{$uid}")->set($customerRecord);

        $this->setSessionCustomer($uid, $customerRecord);

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

        $customer = $this->findCustomerByEmail($request->input('email'));

        if (!$customer || empty($customer['password_hash']) || !Hash::check($request->input('password'), $customer['password_hash'])) {
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

    private function findCustomerByEmail(string $email): ?array
    {
        $snapshot = $this->firebase->getRef('customers')
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();

        if (!$snapshot || !is_array($snapshot)) {
            return null;
        }

        $record = reset($snapshot);
        if (!is_array($record)) {
            return null;
        }

        if (!isset($record['uid'])) {
            $record['uid'] = array_key_first($snapshot);
        }

        return $record;
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
