<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showRegister()
    {
        $roles = Role::orderBy('name')->pluck('name');

        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::exists('roles', 'name')], 
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email'],
            'block' => ['required_if:role,Resident', 'nullable', 'string', 'max:50'],
            'lot' => ['required_if:role,Resident', 'nullable', 'string', 'max:50'],
            'mobile_number' => [
                'required',
                'regex:/^09\d{9}$/',
                'unique:users,mobile_number',
            ],
            'password' => ['required', 'min:8', 'confirmed'],
            'access_code' => ['nullable', 'string'],
        ]);

        // Resident: walang code. Staff at Admin: kailangan ng tamang code mula sa HOA.
        if ($validated['role'] !== 'Resident') {
            $expected = config('hoa.codes.'.$validated['role']);

            if (! $expected || ! hash_equals($expected, (string) $request->access_code)) {
                return back()
                    ->withErrors(['access_code' => 'Invalid access code for this role.'])
                    ->withInput($request->only('role', 'name', 'email', 'block', 'lot', 'mobile_number'));
            }
        }

        $roleId = Role::where('name', $validated['role'])->value('id');

        if (! $roleId) {
            return back()->withErrors([
                'email' => 'Registration is currently unavailable. Please contact the administrator.',
            ])->withInput($request->only('role', 'name', 'email', 'block', 'lot', 'mobile_number'));
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'block' => $validated['block'] ?? null,
            'lot' => $validated['lot'] ?? null,
            'mobile_number' => $validated['mobile_number'],
            'password' => $validated['password'],
            'role_id' => $roleId,
        ]);

        Auth::login($user); #automatic na ni la-log in yung user after registration
        $request->session()->regenerate(); 

        return redirect('/'); #dashboard
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
            'role' => ['required', 'string'],
        ]);

        $roleId = Role::where('name', $validated['role'])->value('id'); # nag lalagay ng role id 

        // Kasama ang role_id sa tseke: papasok lang kung tugma ang email, password, AT role.
        if ($roleId && Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $roleId,
        ])) {
            $request->session()->regenerate();

            return redirect('/');
        }

        // Iisang mensahe para hindi malaman kung alin ang mali.
        return back()
            ->withErrors(['email' => 'Invalid email, password, or role.'])
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