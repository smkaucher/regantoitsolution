<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Admin > Profile: the one admin changes their own name, email and password. Both forms ask for the current
 * password. The admin routes run "auth.session", so a new password signs out every other browser and device.
 */
class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validateWithBag('details', [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
        ], [
            'current_password.required' => 'Enter your current password to save these changes.',
            'current_password.current_password' => 'That is not your current password.',
        ]);

        $emailChanged = strcasecmp($data['email'], $user->email) !== 0;
        $user->forceFill(['name' => trim($data['name']), 'email' => strtolower(trim($data['email']))])->save();

        return redirect()->route('admin.profile')->with('ok', $emailChanged
            ? 'Saved. Sign in with ' . $user->email . ' from now on.'
            : 'Your details are saved.');
    }

    public function password(Request $request)
    {
        $user = $request->user();
        $request->validateWithBag('pass', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(10)->letters()->numbers()],
        ], [
            'current_password.required' => 'Enter your current password.',
            'current_password.current_password' => 'That is not your current password.',
            'password.confirmed' => 'The two new passwords do not match.',
            'password.different' => 'The new password must be different from the current one.',
        ]);

        $user->forceFill(['password' => Hash::make($request->input('password'))])->setRememberToken(null);
        $user->save();
        $request->session()->regenerate();

        return redirect()->route('admin.profile')->with('ok', 'Password changed. Every other browser and device has been signed out.');
    }
}
