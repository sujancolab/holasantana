<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('owner.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'owner_user_id' => ['required', 'string'],
            'owner_password' => ['required', 'string'],
        ]);

        $owner = Owner::where('owner_user_id', $credentials['owner_user_id'])->first();

        if (! $owner || ! Hash::check($credentials['owner_password'], $owner->owner_password)) {
            return back()->withErrors([
                'owner_user_id' => 'These owner credentials do not match our records.',
            ])->onlyInput('owner_user_id');
        }

        $request->session()->regenerate();
        $request->session()->put('owner_id', $owner->id);

        return redirect()->route('owner.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('owner_id');
        $request->session()->regenerateToken();

        return redirect()->route('owner.login');
    }
}
