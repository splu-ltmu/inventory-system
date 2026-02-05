<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Show request form
    public function showRequestForm()
    {
        return view('auth.password-reset-request');
    }

    // Submit password reset request via email (creates admin-reviewable request)
    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'reason' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Check if user already has a pending request
        $existingRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('password-reset.request')
                ->with('error', 'You already have a pending password reset request.');
        }

        // Generate unique token
        $token = Str::random(64);

        // Create new password reset request for admin review
        PasswordResetRequest::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'notes' => $validated['reason'] ?? null,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->route('password-reset.request')
            ->with('success', 'Password reset request submitted. An administrator will review it shortly.');
    }

    // Show the reset form when user clicks the emailed token link
    public function showResetForm($token)
    {
        $resetRequest = PasswordResetRequest::where('token', $token)
            ->whereIn('status', ['pending', 'sent'])
            ->first();

        if (!$resetRequest) {
            return redirect()->route('password-reset.request')
                ->with('error', 'Invalid or expired password reset link.');
        }

        return view('auth.password-reset-form', ['token' => $token, 'email' => $resetRequest->email]);
    }

    // Process the password reset submitted by the user via token link
    public function resetPassword(Request $request, $token)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRequest = PasswordResetRequest::where('token', $token)
            ->whereIn('status', ['pending', 'sent'])
            ->first();

        if (!$resetRequest) {
            return redirect()->route('password-reset.request')
                ->with('error', 'Invalid or expired password reset link.');
        }

        $user = User::find($resetRequest->user_id);
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->save();

        $resetRequest->status = 'completed';
        $resetRequest->resolved_at = now();
        $resetRequest->save();

        return redirect()->route('client.login')->with('success', 'Password updated successfully. Please log in.');
    }
}
