<?php

namespace App\Services;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send email notification if enabled for user and notification type
     */
    public static function sendEmailNotification($userId, $type, $subject, $content, $data = [])
    {
        if (!NotificationPreference::isEmailEnabled($userId, $type)) {
            return false;
        }

        $user = User::find($userId);
        if (!$user || !$user->email) {
            return false;
        }

        try {
            // You can create specific mailable classes for different notification types
            // For now, we'll use a generic approach
            Mail::raw($content, function ($message) use ($user, $subject) {
                $message->to($user->email)
                    ->subject($subject);
            });

            Log::info("Email notification sent to {$user->email} for type: {$type}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send email notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send batch email notifications to multiple users
     */
    public static function sendBatchEmailNotifications($userIds, $type, $subject, $content, $data = [])
    {
        $successCount = 0;
        
        foreach ($userIds as $userId) {
            if (self::sendEmailNotification($userId, $type, $subject, $content, $data)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Get all admin users who have email notifications enabled for specific type
     */
    public static function getAdminsWithEmailEnabled($type)
    {
        return User::where('role', 'admin')
            ->whereHas('notificationPreferences', function ($query) use ($type) {
                $query->where('notification_type', $type)
                      ->where('email_enabled', true);
            })
            ->get();
    }

    /**
     * Notify admins about pending requests
     */
    public static function notifyPendingRequests()
    {
        $admins = self::getAdminsWithEmailEnabled('pending_requests');
        $pendingCount = \App\Models\StockRequest::where('status', 'pending')->count();
        
        if ($pendingCount > 0 && $admins->isNotEmpty()) {
            $subject = "Pending Stock Requests Alert";
            $content = "You have {$pendingCount} pending stock request(s) that need your attention.\n\n";
            $content .= "Please log in to the admin panel to review and process these requests.";
            
            return self::sendBatchEmailNotifications(
                $admins->pluck('id')->toArray(),
                'pending_requests',
                $subject,
                $content
            );
        }

        return 0;
    }

    /**
     * Notify admins about low stock items
     */
    public static function notifyLowStock()
    {
        $admins = self::getAdminsWithEmailEnabled('low_stock');
        $lowThreshold = 5;
        $lowStockItems = \App\Models\Stock::where('stock', '>', 0)
            ->where('stock', '<=', $lowThreshold)
            ->get();
        
        if ($lowStockItems->isNotEmpty() && $admins->isNotEmpty()) {
            $subject = "Low Stock Alert";
            $content = "The following items are running low on stock:\n\n";
            
            foreach ($lowStockItems as $item) {
                $content .= "- {$item->description}: {$item->stock} units remaining\n";
            }
            
            $content .= "\nPlease consider restocking these items soon.";
            
            return self::sendBatchEmailNotifications(
                $admins->pluck('id')->toArray(),
                'low_stock',
                $subject,
                $content
            );
        }

        return 0;
    }

    /**
     * Notify admins about urgent outbound requests
     */
    public static function notifyUrgentOutbounds()
    {
        $admins = self::getAdminsWithEmailEnabled('urgent_outbounds');
        $urgentRequests = \App\Models\Outbound::where('is_urgent_outbound', true)
            ->where('approval', 'pending')
            ->get();
        
        if ($urgentRequests->isNotEmpty() && $admins->isNotEmpty()) {
            $subject = "URGENT: Pending Outbound Requests";
            $content = "You have {$urgentRequests->count()} urgent outbound request(s) pending approval:\n\n";
            
            foreach ($urgentRequests as $request) {
                $content .= "- {$request->stock->description}: {$request->total} units for {$request->recipient_name}\n";
            }
            
            $content .= "\nThese requests require immediate attention.";
            
            return self::sendBatchEmailNotifications(
                $admins->pluck('id')->toArray(),
                'urgent_outbounds',
                $subject,
                $content
            );
        }

        return 0;
    }

    /**
     * Notify admins about system health issues
     */
    public static function notifySystemHealth()
    {
        $admins = self::getAdminsWithEmailEnabled('system_health');
        
        try {
            $failedJobs = \DB::table('failed_jobs')->count();
            
            if ($failedJobs > 0 && $admins->isNotEmpty()) {
                $subject = "System Health Alert";
                $content = "System health issue detected:\n\n";
                $content .= "- Failed jobs: {$failedJobs}\n";
                $content .= "\nPlease check the system health dashboard for more details.";
                
                return self::sendBatchEmailNotifications(
                    $admins->pluck('id')->toArray(),
                    'system_health',
                    $subject,
                    $content
                );
            }
        } catch (\Exception $e) {
            Log::error("Error checking system health: " . $e->getMessage());
        }

        return 0;
    }
}
