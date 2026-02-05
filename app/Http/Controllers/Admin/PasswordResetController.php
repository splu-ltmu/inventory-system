<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Show request form
    public function showRequestForm()
    {
        return view('auth.password-reset-request');
    }

    // Submit password reset request via email
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

        // Create new password reset request
        PasswordResetRequest::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'notes' => $validated['reason'],
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Send reset email
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $token));
            
            return redirect()->route('password-reset.request')
                ->with('success', 'Password reset link has been sent to your email. Please check your inbox.');
        } catch (\Exception $e) {
            // If email fails, delete the token request
            PasswordResetRequest::where('token', $token)->delete();
            
            return redirect()->route('password-reset.request')
                ->with('error', 'Failed to send reset email. Please try again later.');
        }
    }

    // Handle password reset via token
    public function showResetForm($token)
    {
        $resetRequest = PasswordResetRequest::where('token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$resetRequest) {
            return redirect()->route('admin.login')
                ->with('error', 'Invalid or expired password reset link.');
        }

        // Admin-only view for resetting a user's password using the token
        return view('admin.password-reset.form', ['token' => $token, 'email' => $resetRequest->email, 'request' => $resetRequest]);
    }

    // Process password reset
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRequest = PasswordResetRequest::where('token', $validated['token'])
            ->where('status', 'pending')
            ->first();

        if (!$resetRequest) {
            return redirect()->route('admin.login')
                ->with('error', 'Invalid or expired password reset link.');
        }

        $user = User::find($resetRequest->user_id);
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Mark reset as completed
        $resetRequest->status = 'completed';
        $resetRequest->resolved_at = now();
        $resetRequest->save();

        // Notify the user that their password was changed by an admin
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\AdminPasswordChangedMail($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send admin-password-changed email: ' . $e->getMessage());
        }

        return redirect()->route('password-reset.index')
            ->with('success', 'Password has been reset successfully. The user has been notified.');
    }

    // Admin: List all requests
    public function index()
    {
        $requests = PasswordResetRequest::with('user')->orderBy('requested_at', 'desc')->get();
        return view('admin.password-reset.index', compact('requests'));
    }

    // Admin: Approve request (deprecated, but kept for backward compatibility)
    public function approve($id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);
        // Mark request as sent and email the user a reset link
        $resetRequest->status = 'sent';
        $resetRequest->resolved_at = now();
        $resetRequest->save();

        $user = User::find($resetRequest->user_id);

        try {
            // Use sendNow() to bypass queue
            Mail::to($user->email)->sendNow(new PasswordResetMail($user, $resetRequest->token));
            \Log::info('Password reset link sent to ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset link: ' . $e->getMessage());
            return redirect()->route('password-reset.index')
                ->with('error', 'Failed to send reset link. Please try again.');
        }

        return redirect()->route('password-reset.index')
            ->with('success', 'Password reset link sent to user.');
    }

    // Admin: Reject request
    public function reject($id, Request $request)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);
        
        $resetRequest->status = 'rejected';
        $resetRequest->resolved_at = now();
        $resetRequest->notes = ($resetRequest->notes ?? '') . ' | Rejected: ' . ($request->input('notes', 'Rejected by admin'));
        $resetRequest->save();
        
        return redirect()->route('password-reset.index')
            ->with('success', 'Password reset request rejected.');
    }

    // Admin self-reset: Show form to enter their email
    public function showSelfResetForm()
    {
        return view('admin.password-reset.self-form');
    }

    // Admin self-reset: Send reset link directly (no approval needed)
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find the user
        $user = User::where('email', $validated['email'])->first();

        // Only admin can reset their own password
        if ($user->role !== 'admin') {
            return redirect()->route('admin.password-reset-self.form')
                ->with('error', 'You can only reset your own admin account.');
        }

        // Generate token
        $token = Str::random(64);

        // Create password reset request with 'sent' status (skip pending/approval)
        PasswordResetRequest::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'notes' => 'Admin self-reset',
            'status' => 'sent',
            'requested_at' => now(),
            'resolved_at' => null,
        ]);

        // Send reset link via email (synchronously, not queued)
        try {
            // Use sendNow() instead of send() to skip the queue
            Mail::to($user->email)->sendNow(new PasswordResetMail($user, $token));
            
            \Log::info('Password reset email sent successfully to ' . $user->email);
            
            return redirect()->route('admin.password-reset-self.form')
                ->with('success', 'Password reset link has been sent to your email.');
        } catch (\Exception $e) {
            // Clean up on failure
            PasswordResetRequest::where('token', $token)->delete();
            
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            
            return redirect()->route('admin.password-reset-self.form')
                ->with('error', 'Failed to send reset email: ' . $e->getMessage());
        }
    }
}
