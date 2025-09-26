<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

/**
 * ========================================
 * AUTH CONTROLLER - GESTION DE L'AUTHENTIFICATION
 * ========================================
 * 
 * Ce contrôleur gère l'authentification des utilisateurs.
 * Il traite la connexion, déconnexion et redirection après authentification
 * pour sécuriser l'accès aux fonctionnalités d'administration.
 */
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(){
        return view('auth.login');
    }

    public function doLogin(LoginRequest $request){
        $credentials = $request->validated();
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));

        }

    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
