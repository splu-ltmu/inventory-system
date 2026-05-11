<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'enabled',
        'email_enabled',
        'settings',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get the user that owns the notification preference.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default notification types
     */
    public static function getDefaultTypes()
    {
        return [
            'pending_requests' => 'Pending Stock Requests',
            'password_resets' => 'Password Reset Requests',
            'low_stock' => 'Low Stock Alerts',
            'out_of_stock' => 'Out of Stock Alerts',
            'urgent_outbounds' => 'Urgent Outbound Requests',
            'expiring_items' => 'Expiring Items',
            'new_clients' => 'New Client Registrations',
            'system_health' => 'System Health Alerts',
        ];
    }

    /**
     * Get or create preference for user and type
     */
    public static function getOrCreate($userId, $type)
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'notification_type' => $type],
            ['enabled' => true, 'email_enabled' => false]
        );
    }

    /**
     * Check if notification type is enabled for user
     */
    public static function isEnabled($userId, $type)
    {
        $preference = self::where('user_id', $userId)
            ->where('notification_type', $type)
            ->first();

        return $preference ? $preference->enabled : true; // Default to enabled
    }

    /**
     * Check if email notification is enabled for user and type
     */
    public static function isEmailEnabled($userId, $type)
    {
        $preference = self::where('user_id', $userId)
            ->where('notification_type', $type)
            ->first();

        return $preference ? $preference->email_enabled : false; // Default to disabled
    }
}
