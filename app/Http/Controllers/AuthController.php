<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email'],
            'block' => ['required', 'string', 'max:50'],
            'lot' => ['required', 'string', 'max:50'],
            'mobile_number' => [
                'required',
                'regex:/^09\d{9}$/',
                'unique:users,mobile_number',
            ],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // All newly registered accounts are Residents.
        $validated['role_id'] = Role::where('name', 'Resident')->value('id');

        if (!$validated['role_id']) {
            return back()->withErrors([
                'email' => 'Registration is currently unavailable. Please contact the administrator.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create($validated);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => [
                'required',
                'string',
                'in:HOA Admin,Resident,Facility Staff',
            ],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // The selected role must match the account's database role.
            if ($user->role?->name !== $validated['role']) {
                Auth::logout();

                return back()
                    ->withErrors([
                        'role' => 'The selected role does not match your account.',
                    ])
                    ->withInput($request->only('email', 'role'));
            }

            $request->session()->regenerate();

            return redirect('/');
        }

        return back()
            ->withErrors([
                'email' => 'Invalid email or password.',
            ])
            ->withInput($request->only('email', 'role'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
