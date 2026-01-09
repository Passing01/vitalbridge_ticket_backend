<?php

namespace App\Http\Controllers\Qmatic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Afficher la page de connexion
     */
    public function showLoginForm()
    {
        if (Auth::guard('qmatic')->check()) {
            return redirect()->route('qmatic.agent.dashboard');
        }
        
        return view('qmatic.auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('qmatic')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Vérifier si l'utilisateur est actif
            if (!Auth::guard('qmatic')->user()->is_active) {
                Auth::guard('qmatic')->logout();
                return back()->withErrors([
                    'username' => 'Votre compte a été désactivé.',
                ]);
            }

            return redirect()->intended(route('qmatic.agent.dashboard'));
        }

        return back()->withErrors([
            'username' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('username');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::guard('qmatic')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('qmatic.login');
    }
}
