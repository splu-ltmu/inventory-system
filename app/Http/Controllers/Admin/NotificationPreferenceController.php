<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    /**
     * Display notification preferences for the authenticated admin user
     */
    public function index()
    {
        $user = auth()->user();
        $preferences = [];
        
        foreach (NotificationPreference::getDefaultTypes() as $type => $label) {
            $preference = NotificationPreference::getOrCreate($user->id, $type);
            $preferences[$type] = [
                'label' => $label,
                'enabled' => $preference->enabled,
                'email_enabled' => $preference->email_enabled,
                'settings' => $preference->settings ?? [],
            ];
        }

        return view('admin.notification-preferences', compact('preferences'));
    }

    /**
     * Update notification preferences
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.enabled' => 'boolean',
            'preferences.*.email_enabled' => 'boolean',
        ]);

        foreach ($validated['preferences'] as $type => $data) {
            $preference = NotificationPreference::getOrCreate($user->id, $type);
            $preference->update([
                'enabled' => $data['enabled'] ?? true,
                'email_enabled' => $data['email_enabled'] ?? false,
            ]);
        }

        return redirect()->route('admin.notification-preferences.index')
            ->with('success', 'Notification preferences updated successfully.');
    }

    /**
     * Get notification counts respecting user preferences and read status
     */
    public function getFilteredCounts()
    {
        $user = auth()->user();
        
        // Get read notifications from session
        $readKey = 'admin_read_notifications_' . $user->id;
        $currentRead = session($readKey, []);
        
        // Get all notifications using the same logic as the notifications page
        $notifications = $this->getAdminNotificationsForCounting($user, $currentRead);
        
        // Filter counts based on user preferences and read status
        $filteredCounts = [
            'pendingRequests' => 0,
            'pendingPasswordResets' => 0,
            'lowStock' => 0,
            'outStock' => 0,
            'urgentOutbounds' => 0,
            'expiringItems' => 0,
            'recentClients' => 0,
            'failedJobs' => 0,
        ];

        foreach ($notifications as $notification) {
            // Only count unread notifications
            if (!$notification->read && NotificationPreference::isEnabled($user->id, $notification->type)) {
                switch($notification->type) {
                    case 'pending_requests':
                        $filteredCounts['pendingRequests']++;
                        break;
                    case 'password_resets':
                        $filteredCounts['pendingPasswordResets']++;
                        break;
                    case 'low_stock':
                        $filteredCounts['lowStock']++;
                        break;
                    case 'out_of_stock':
                        $filteredCounts['outStock']++;
                        break;
                    case 'urgent_outbounds':
                        $filteredCounts['urgentOutbounds']++;
                        break;
                    case 'expiring_items':
                        $filteredCounts['expiringItems']++;
                        break;
                    case 'new_clients':
                        $filteredCounts['recentClients']++;
                        break;
                    case 'system_health':
                        $filteredCounts['failedJobs']++;
                        break;
                }
            }
        }

        $total = array_sum($filteredCounts);
        $filteredCounts['total'] = $total;

        return response()->json($filteredCounts);
    }

    /**
     * Get admin notifications for counting (simplified version)
     */
    private function getAdminNotificationsForCounting($user, $currentRead = [])
    {
        $notifications = collect();
        
        // Pending requests
        $pendingRequests = \App\Models\StockRequest::where('status', 'pending')->get();
        foreach ($pendingRequests as $request) {
            $notificationId = 'pending_' . $request->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'pending_requests',
                'read' => $isRead,
            ]);
        }

        // Password reset requests
        $pendingPasswordResets = \App\Models\PasswordResetRequest::where('status', 'pending')->get();
        foreach ($pendingPasswordResets as $reset) {
            $notificationId = 'password_' . $reset->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'password_resets',
                'read' => $isRead,
            ]);
        }

        // Low stock alerts
        $lowThreshold = 49;
        $lowStock = \App\Models\Stock::where('stock','>',0)->where('stock','<=',$lowThreshold)->get();
        foreach ($lowStock as $stock) {
            $notificationId = 'low_' . $stock->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'low_stock',
                'read' => $isRead,
            ]);
        }

        // Out of stock alerts
        $outStock = \App\Models\Stock::where('stock','<=',0)->get();
        foreach ($outStock as $stock) {
            $notificationId = 'out_' . $stock->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'out_of_stock',
                'read' => $isRead,
            ]);
        }

        // Urgent outbounds
        $urgentOutbounds = \App\Models\Outbound::where('is_urgent_outbound', true)
            ->where('approval', 'pending')
            ->get();
        foreach ($urgentOutbounds as $urgent) {
            $notificationId = 'urgent_' . $urgent->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'urgent_outbounds',
                'read' => $isRead,
            ]);
        }

        // Expiring items
        $expiringItems = collect();
        if (\Illuminate\Support\Facades\Schema::hasColumn('stocks', 'expiry_date')) {
            $sevenDaysFromNow = \Carbon\Carbon::now()->addDays(7);
            $expiringItems = \App\Models\Stock::where('expiry_date', '<=', $sevenDaysFromNow)
                ->where('expiry_date', '>', \Carbon\Carbon::now())
                ->where('stock', '>', 0)
                ->get();
        }
        foreach ($expiringItems as $item) {
            $notificationId = 'expiring_' . $item->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'expiring_items',
                'read' => $isRead,
            ]);
        }

        // Recent clients
        $recentClients = \App\Models\User::where('role', 'client')
            ->where('created_at', '>=', \Carbon\Carbon::now()->subHours(24))
            ->get();
        foreach ($recentClients as $client) {
            $notificationId = 'client_' . $client->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'new_clients',
                'read' => $isRead,
            ]);
        }

        // System health alerts
        $failedJobs = 0;
        try {
            $failedJobs = \DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            // Table might not exist, ignore
        }

        if ($failedJobs > 0) {
            $notificationId = 'system_health';
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'system_health',
                'read' => $isRead,
            ]);
        }

        return $notifications;
    }
}
