<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientNotificationPreferenceController extends Controller
{
    /**
     * Display notification preferences for client user
     */
    public function index()
    {
        // For now, we'll use a simple approach
        // In a full implementation, you'd store these in database
        return view('client.notification-preferences');
    }

    /**
     * Update notification preferences
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.enabled' => 'boolean',
        ]);

        // For now, we'll just store in session
        // In a full implementation, you'd store in database
        session(['client_notification_preferences' => $validated['preferences']]);

        return redirect()->route('client.notification-preferences.index')
            ->with('success', 'Notification preferences updated successfully.');
    }
}
