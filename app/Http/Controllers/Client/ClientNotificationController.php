<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\ClientMember;
use App\Models\ClientMemberDistribution;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientNotificationController extends Controller
{
    /**
     * Display client notifications page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get client-specific notifications
        $notifications = $this->getClientNotifications($user);
        
        // Mark member activity notifications as viewed
        $this->markMemberActivityAsViewed($user);
        
        return view('client.notifications', compact('notifications'));
    }

    /**
     * Get notification counts for client
     */
    public function counts()
    {
        $user = Auth::user();
        $notifications = $this->getClientNotifications($user);
        
        $counts = [
            'pending_requests' => 0,
            'approved_requests' => 0,
            'ready_to_receive' => 0,
            'rejected_requests' => 0,
            'low_inventory' => 0,
            'member_activity' => 0,
            'total' => 0,
        ];

        foreach ($notifications as $notification) {
            // Only count unread notifications
            if (!$notification->read) {
                $counts[$notification->type]++;
                $counts['total']++;
            }
        }

        return response()->json($counts);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $readKey = 'client_read_notifications_' . $user->id;
        $currentRead = session($readKey, []);
        
        if (!in_array($id, $currentRead)) {
            $currentRead[] = $id;
            session([$readKey => $currentRead]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $notifications = $this->getClientNotifications($user);
        $readKey = 'client_read_notifications_' . $user->id;
        $currentRead = session($readKey, []);
        
        // Mark all current notifications as read
        foreach ($notifications as $notification) {
            if (!in_array($notification->id, $currentRead)) {
                $currentRead[] = $notification->id;
            }
        }
        
        session([$readKey => $currentRead]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Get client notifications based on their role and data
     */
    private function getClientNotifications($user)
    {
        $notifications = collect();
        
        // Get read notifications from session
        $readKey = 'client_read_notifications_' . $user->id;
        $currentRead = session($readKey, []);

        // Client-specific notifications
        $notifications = $notifications->merge($this->getClientRequestNotifications($user, $currentRead));
        $notifications = $notifications->merge($this->getClientInventoryNotifications($user, $currentRead));
        $notifications = $notifications->merge($this->getClientMemberNotifications($user));

        return $notifications->sortByDesc('created_at');
    }

    /**
     * Get client request notifications
     */
    private function getClientRequestNotifications($user, $currentRead = [])
    {
        $notifications = collect();
        
        // Pending requests
        $pendingRequests = StockRequest::where('client_id', $user->id)
            ->where('status', 'pending')
            ->get();

        foreach ($pendingRequests as $request) {
            $notificationId = 'pending_' . $request->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'pending_requests',
                'title' => 'Request Pending',
                'message' => "Request #{$request->id} is pending approval",
                'created_at' => $request->created_at,
                'read' => $isRead,
                'action_url' => route('client.requests'),
                'icon' => 'clock',
                'color' => 'orange'
            ]);
        }
        
        // Approved requests (recent)
        $approvedRequests = StockRequest::where('client_id', $user->id)
            ->where('status', 'approved')
            ->where('updated_at', '>=', now()->subDays(7))
            ->get();

        foreach ($approvedRequests as $request) {
            $notificationId = 'approved_' . $request->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'approved_requests',
                'title' => 'Request Approved',
                'message' => "Request #{$request->id} has been approved",
                'created_at' => $request->updated_at,
                'read' => $isRead,
                'action_url' => route('client.requests'),
                'icon' => 'check-circle',
                'color' => 'green'
            ]);
        }
        
        // Ready to receive requests
        $readyToReceive = StockRequest::where('client_id', $user->id)
            ->where('status', 'ready_to_receive')
            ->where('updated_at', '>=', now()->subDays(7))
            ->get();

        foreach ($readyToReceive as $request) {
            $notificationId = 'ready_' . $request->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'ready_to_receive',
                'title' => 'Ready to Receive',
                'message' => "Request #{$request->id} is ready for pickup",
                'created_at' => $request->updated_at,
                'read' => $isRead,
                'action_url' => route('client.requests'),
                'icon' => 'package',
                'color' => 'blue'
            ]);
        }

        // Rejected requests
        $rejectedRequests = StockRequest::where('client_id', $user->id)
            ->where('status', 'cancelled')
            ->where('updated_at', '>=', now()->subDays(7))
            ->get();

        foreach ($rejectedRequests as $request) {
            $notificationId = 'rejected_' . $request->id;
            $isRead = in_array($notificationId, $currentRead);
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'rejected_requests',
                'title' => 'Request Rejected',
                'message' => "Your request #{$request->id} was not approved",
                'created_at' => $request->updated_at,
                'read' => $isRead,
                'action_url' => route('client.requests'),
                'icon' => 'x-circle',
                'color' => 'red'
            ]);
        }

        return $notifications;
    }

    /**
     * Get client inventory notifications
     */
    private function getClientInventoryNotifications($user, $currentRead = [])
    {
        $notifications = collect();
        
        // Get client's inventory items
        $inventoryItems = StockRequestItem::with(['stock'])
            ->whereHas('request', function($query) use ($user) {
                $query->where('client_id', $user->id)
                      ->whereIn('status', ['approved', 'ready_to_receive', 'released']);
            })
            ->where('approved_qty', '>', 0)
            ->get();

        // Check for low inventory items
        foreach ($inventoryItems as $item) {
            $availableQty = max(0, ($item->approved_qty ?? 0) - ($item->distributed_qty ?? 0));
            
            if ($availableQty > 0 && $availableQty <= 5) {
                $notificationId = 'low_inventory_' . $item->id;
                $isRead = in_array($notificationId, $currentRead);
                
                $notifications->push((object)[
                    'id' => $notificationId,
                    'type' => 'low_inventory',
                    'title' => 'Low Inventory Alert',
                    'message' => "{$item->stock->description} has only {$availableQty} remaining",
                    'created_at' => $item->updated_at,
                    'read' => $isRead,
                    'action_url' => route('client.inventory'),
                    'icon' => 'alert-triangle',
                    'color' => 'orange'
                ]);
            }
        }

        return $notifications;
    }

    /**
     * Get client member notifications
     */
    private function getClientMemberNotifications($user)
    {
        $notifications = collect();
        
        // Recent member activity
        $recentMembers = ClientMember::where('client_id', $user->id)
            ->where('created_at', '>=', now()->subDays(3))
            ->get();

        foreach ($recentMembers as $member) {
            $notificationId = 'new_member_' . $member->id;
            $isViewed = $this->isMemberActivityViewed($user, (object)['id' => $notificationId, 'type' => 'member_activity']);
            
            // Completely skip viewed member activity notifications
            if ($isViewed) {
                continue;
            }
            
            $notifications->push((object)[
                'id' => $notificationId,
                'type' => 'member_activity',
                'title' => 'New Member Added',
                'message' => "{$member->name} has been added to your team",
                'created_at' => $member->created_at,
                'read' => false, // Always false since we only show unviewed ones
                'action_url' => route('client.account', ['tab' => 'members']),
                'icon' => 'user-plus',
                'color' => 'blue'
            ]);
        }

        return $notifications;
    }

    /**
     * Mark member activity notifications as viewed
     */
    private function markMemberActivityAsViewed($user)
    {
        $viewedKey = 'client_viewed_member_activity_' . $user->id;
        $currentViewed = session($viewedKey, []);
        
        // Get all member activity notifications for this user
        $memberNotifications = $this->getClientMemberNotifications($user);
        
        // Mark all current member activity notifications as viewed
        foreach ($memberNotifications as $notification) {
            if (!in_array($notification->id, $currentViewed)) {
                $currentViewed[] = $notification->id;
            }
        }
        
        session([$viewedKey => $currentViewed]);
    }

    /**
     * Check if a member activity notification has been viewed
     */
    private function isMemberActivityViewed($user, $notification)
    {
        if ($notification->type !== 'member_activity') {
            return false;
        }
        
        $viewedKey = 'client_viewed_member_activity_' . $user->id;
        $currentViewed = session($viewedKey, []);
        
        return in_array($notification->id, $currentViewed);
    }

}
