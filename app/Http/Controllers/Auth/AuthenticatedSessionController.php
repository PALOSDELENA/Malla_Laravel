<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Usuarios;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(route('dashboard', absolute: false));
    // }

    public function store(LoginRequest $request): RedirectResponse
    {
        $usu_email = $request->get('email');
        $password = $request->get('password');

        $usuario = User::where('email', $usu_email)->first();

        if (!$usuario || !$usuario->seguridad) {
            return back()->withErrors([
                'email' => 'Credenciales incorrectas.',
            ]);
        }

        $hashedPassword = $usuario->seguridad->seg_credencial;

        if (!$hashedPassword || !str_starts_with($hashedPassword, '$2y$')) {
            $hashedPassword = Hash::make($password);
            $usuario->seguridad()->update(['seg_credencial' => $hashedPassword]);
        }

        if (!Hash::check($password, $hashedPassword)) {
            return back()->withErrors([
                'password' => 'Contraseña incorrecta.',
            ]);
        }

        $request->session()->regenerate();
        Auth::login($usuario);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
