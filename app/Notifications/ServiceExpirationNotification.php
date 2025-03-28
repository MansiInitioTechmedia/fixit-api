<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class ServiceExpirationNotification extends Notification implements ShouldQueue {

    use Queueable;

    protected $service;


    public function __construct($service) {

        $this->service = $service;

    }


    public function via($notifiable) {

        return ['mail']; // You can also use 'database' or 'sms' if needed.

    }



    public function toMail($notifiable) {

        return (new MailMessage)
            ->subject('Upcoming Service Expiration Alert')
            ->greeting("Hello,")
            ->line("Your **{$this->service->service_type}** for vehicle **{$this->service->vehicle->name}** is expiring soon!")
            ->line("Expiration Date: **{$this->service->expiration_date}**")
            ->action('View Details', url('/services/' . $this->service->id))
            ->line('Please take the necessary action before expiration.');
    }
    
}
