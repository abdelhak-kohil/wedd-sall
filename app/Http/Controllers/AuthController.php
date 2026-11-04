<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse e-mail valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte a été désactivé par l\'administrateur.',
                ]);
            }

            $request->session()->regenerate();

            // Set user's preferred language in session
            Session::put('locale', $user->preferred_language ?: 'fr');

            // Audit log
            if ($user->business_id) {
                AuditLog::create([
                    'business_id' => $user->business_id,
                    'user_id' => $user->id,
                    'action' => 'LOGIN',
                    'auditable_type' => get_class($user),
                    'auditable_id' => $user->id,
                    'new_values' => ['ip' => $request->ip()],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Identifiants incorrects. Veuillez vérifier votre e-mail et mot de passe.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user && $user->business_id) {
            AuditLog::create([
                'business_id' => $user->business_id,
                'user_id' => $user->id,
                'action' => 'LOGOUT',
                'auditable_type' => get_class($user),
                'auditable_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function switchLanguage(string $locale): RedirectResponse
    {
        if (in_array($locale, ['fr', 'ar'], true)) {
            Session::put('locale', $locale);

            if (Auth::check()) {
                Auth::user()->update(['preferred_language' => $locale]);
            }
        }

        return back();
    }
}
