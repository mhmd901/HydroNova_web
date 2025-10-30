<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\FirebaseService;

class AdminController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Show admin login form
     */
    public function login()
    {
        // Redirect to dashboard if already logged in
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Verify admin credentials (with Firebase)
     */
    public function checkLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Fetch credentials from Firebase (settings/admin)
        $adminData = $this->firebase->getRef('settings/admin')->getValue();

        if ($adminData &&
            $request->username === $adminData['username'] &&
            (
                // Support both hashed and plain text for flexibility
                Hash::check($request->password, $adminData['password']) ||
                $request->password === $adminData['password']
            )
        ) {
            // ✅ Login success → store session
            session([
                'admin_logged_in' => true,
                'admin_username' => $adminData['username'],
                'admin_role' => $adminData['role'] ?? 'superadmin',
            ]);

            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    /**
     * Admin dashboard view
     */
    public function dashboard()
    {
        $products = $this->firebase->getAll('products');
        $plans = $this->firebase->getAll('plans');

        return view('admin.dashboard', compact('products', 'plans'));
    }

    /**
     * Show admin settings page
     */
    public function settings()
    {
        $admin = $this->firebase->getRef('settings/admin')->getValue();
        return view('admin.settings', compact('admin'));
    }

    /**
     * Update admin credentials (username & password)
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'username' => 'required|min:3',
            'password' => 'required|min:4',
        ]);

        $updateData = [
            'username' => $request->username,
            // Store password hashed for security
            'password' => bcrypt($request->password),
            'role' => 'superadmin'
        ];

        $this->firebase->getRef('settings/admin')->update($updateData);

        return redirect()->route('admin.settings')
                         ->with('success', 'Credentials updated successfully!');
    }

    /**
     * Logout admin
     */
    public function logout()
    {
        session()->forget(['admin_logged_in', 'admin_username', 'admin_role']);
        return redirect()->route('admin.login');
    }

    /**
     * Display all messages sent via the contact form
     */
    public function messages()
    {
        $messages = $this->firebase->getAll('messages');
        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Delete a specific message
     */
    public function deleteMessage($id)
    {
        $this->firebase->getRef('messages/' . $id)->remove();

        return redirect()->route('admin.messages.index')
                         ->with('success', 'Message deleted successfully!');
    }
}
