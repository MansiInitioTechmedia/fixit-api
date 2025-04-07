<?php

namespace App\Http\Controllers\Api;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;

class PushNotificationController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if (!$user->fcm_token) {
            return response()->json([
                'status' => false,
                'message' => 'FCM token not found for the user.',
            ], 404);
        }

        $today = Carbon::today();
        $schedules = Schedule::where('user_id', $user->id)->with('category')->get();

        $notifications = [];
        $reminders = [];

        foreach ($schedules as $schedule) {
            $categoryName = optional($schedule->category)->name ?? 'Service';
            $serviceDate = $schedule->service_date ? Carbon::parse($schedule->service_date) : null;
            $expirationDate = $schedule->expiration_date ? Carbon::parse($schedule->expiration_date) : null;
            $kilometers = $schedule->kilometers;

            // 🔔 Notifications: Service due today
            if ($serviceDate && $serviceDate->isSameDay($today)) {
                $notifications[] = $this->sendNotification($user, "{$categoryName} Due Today", "Hi {$user->name}, your {$categoryName} is due today ({$serviceDate->format('d M, Y')}).");
            }

            // ⏰ Reminders: expiration coming soon (1–5 days)
            if ($expirationDate) {
                $daysLeft = $today->diffInDays($expirationDate, false);
                if ($daysLeft >= 1 && $daysLeft <= 5) {
                    $reminders[] = $this->sendNotification($user, "{$categoryName} Expiry Reminder", "Hi {$user->name}, your {$categoryName} is expiring in {$daysLeft} day" . ($daysLeft > 1 ? 's' : '') . " ({$expirationDate->format('d M, Y')}).");
                }
                // 🔔 Notifications: already expired
                elseif ($expirationDate->isBefore($today)) {
                    $notifications[] = $this->sendNotification($user, "{$categoryName} Expired", "Hi {$user->name}, your {$categoryName} expired on {$expirationDate->format('d M, Y')}. Please take action.");
                }
            }

            // 🔔 Notifications: kilometer-based
            if ($kilometers >= 5000 && $kilometers < 10000) {
                $notifications[] = $this->sendNotification($user, "{$categoryName} - Oil Change Alert", "Hi {$user->name}, your vehicle has crossed 5,000 KM for {$categoryName}. Time for an oil change.");
            }

            if ($kilometers >= 10000 && $kilometers < 20000) {
                $notifications[] = $this->sendNotification($user, "{$categoryName} - PUC Check Due", "Hi {$user->name}, your vehicle has crossed 10,000 KM for {$categoryName}. Time for a PUC check.");
            }

            if ($kilometers >= 20000 && $kilometers < 25000) {
                $notifications[] = $this->sendNotification($user, "{$categoryName} - Tyre Replacement", "Hi {$user->name}, your vehicle has crossed 20,000 KM for {$categoryName}. Consider replacing the tyres.");
            }

            if ($kilometers >= 25000) {
                $notifications[] = $this->sendNotification($user, "{$categoryName} - Part Replacement", "Hi {$user->name}, your vehicle has crossed 25,000 KM for {$categoryName}. Part replacement may be required.");
            }
        }

        return response()->json([
            'status' => true,
            'message' => count($notifications) + count($reminders) > 0 ? 'Notifications processed.' : 'No notifications generated.',
            'notifications' => $notifications,
            'reminders' => $reminders,
        ]);
    }

    protected function sendNotification($user, $title, $body)
    {
        $status = $this->firebase->sendToDevice($user->fcm_token, $title, $body);

        NotificationLog::create([
            'user_id' => $user->id,
            'token'   => $user->fcm_token,
            'title'   => $title,
            'body'    => $body,
            'status'  => $status ? '1' : '0',
        ]);

        return [
            'title'  => $title,
            'body'   => $body,
            'status' => $status ? '1' : '0',
        ];
    }
}
