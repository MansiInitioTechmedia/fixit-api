<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use Carbon\Carbon;
use App\Notifications\ServiceExpirationNotification;


class NotifyUpcomingServices extends Command {

    protected $signature = 'notify:upcoming-services';

    protected $description = 'Send notifications for upcoming service expirations';


    public function __construct() {

        parent::__construct();

    }


    public function handle() {

        $today = Carbon::now();
        $notificationBeforeDays = 7; // Notify 7 days before expiration


        $services = Service::whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $today->addDays($notificationBeforeDays))
            ->get();


        foreach ($services as $service) {
            
            if ($service->vehicle->user) {  // Assuming a vehicle belongs to a user
                $service->vehicle->user->notify(new ServiceExpirationNotification($service));
            }

        }


        $this->info('Upcoming service notifications sent successfully!');
    }
    
}
