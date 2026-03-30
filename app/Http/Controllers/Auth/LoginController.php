<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember-me'))) {
            $request->session()->regenerate();
            
            // Get the intended URL from session
            $intended = session()->get('url.intended');
            
            // Clear the intended URL from session
            session()->forget('url.intended');
            
            // Redirect to intended URL, or dashboard if no intended URL
            if ($intended && $intended !== route('dashboard')) {
                return redirect($intended)
                    ->with('success', 'Welcome back! You have successfully logged in.');
            } else {
                return redirect()->route('dashboard')
                    ->with('success', 'Welcome back! You have successfully logged in.');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
