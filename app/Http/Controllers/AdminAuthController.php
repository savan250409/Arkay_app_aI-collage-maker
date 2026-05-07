<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (\Illuminate\Support\Facades\Auth::guard('admin')->attempt($credentials, $request->remember)) {
                $request->session()->regenerate();

                return redirect()->intended('dashboard');
            }

            return back()->withInput()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        } catch (\Exception $e) {
            // Log the full error message, line number and file path
            \Illuminate\Support\Facades\Log::error('Login 500 Debug: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw if in local, otherwise return back with the error message
            if (config('app.debug')) {
                throw $e;
            }

            return back()->withInput()->withErrors([
                'error' => 'An unexpected server error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
