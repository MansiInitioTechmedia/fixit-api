<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Carbon\Carbon;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotificationToToken(string $deviceToken, string $title, string $body)
    {
        $this->sendToDevice($deviceToken, $title, $body);
        return 'Single notification sent to token.';
    }

    public function sendToDevice(string $token, string $title, string $body, array $data = [])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification)
            ->withData($data);

        try {
            $this->messaging->send($message);
            Log::info("Notification sent to token: $token");
            return true;
        } catch (\Throwable $e) {
            Log::error("Error sending notification to $token: " . $e->getMessage());
            return false;
        }
    }

    public function sendScheduledNotifications()
    {
        $today = Carbon::today();
        $schedules = Schedule::with('user')->get();

        foreach ($schedules as $schedule) {
            $user = $schedule->user;

            if (!$user || !$user->fcm_token) continue;

            $messages = [];

            $serviceDate = $schedule->service_date ? Carbon::parse($schedule->service_date) : null;
            $expirationDate = $schedule->expiration_date ? Carbon::parse($schedule->expiration_date) : null;

            // Upcoming service (tomorrow)
            if ($serviceDate && $serviceDate->isSameDay($today->copy()->addDay())) {
                $messages[] = [
                    'title' => 'Upcoming Service',
                    'body'  => "Hi {$user->name}, your vehicle service is scheduled for tomorrow ({$serviceDate->format('d M, Y')}).",
                    'type'  => 'reminder',
                ];
            }

            // Due today
            if ($serviceDate && $serviceDate->isSameDay($today)) {
                $messages[] = [
                    'title' => 'Service Due Today',
                    'body'  => "Hi {$user->name}, your vehicle service is due today ({$serviceDate->format('d M, Y')}). Please visit your service center.",
                    'type'  => 'reminder',
                ];
            }

            // Expired
            if ($expirationDate && $expirationDate->isBefore($today)) {
                $messages[] = [
                    'title' => 'Service Expired',
                    'body'  => "Hi {$user->name}, your service expired on {$expirationDate->format('d M, Y')}. Please take action.",
                    'type'  => 'notification',
                ];
            }

            // Expiring soon (in 1–5 days)
            if ($expirationDate) {
                $daysLeft = $today->diffInDays($expirationDate, false);
                if ($daysLeft > 0 && $daysLeft <= 5) {
                    $messages[] = [
                        'title' => 'Service Expiry Approaching',
                        'body'  => "Hi {$user->name}, your vehicle service will expire on {$expirationDate->format('d M, Y')} (in {$daysLeft} day" . ($daysLeft > 1 ? 's' : '') . ").",
                        'type'  => 'reminder',
                    ];
                }
            }

            // Kilometer-based alert
            if ($schedule->kilometers >= 5000) {
                $messages[] = [
                    'title' => 'Mileage Limit Reached',
                    'body'  => "Hi {$user->name}, your vehicle has exceeded 5000 KM. Please consider a checkup.",
                    'type'  => 'notification',
                ];
            }

            // Send notifications
            foreach ($messages as $msg) {
                $this->sendToDevice(
                    $user->fcm_token,
                    $msg['title'],
                    $msg['body'],
                    [
                        'type' => $msg['type'],
                        'schedule_id' => $schedule->id,
                        'vehicle_id' => $schedule->vehicle_id,
                    ]
                );
            }
        }

        Log::info('Scheduled notifications sent successfully.');
    }
}
