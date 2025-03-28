<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class Schedule extends Model {
    use HasFactory, Notifiable;

    protected $fillable = [
        'category_id', 
        'vehicle_id', 
        'start_date', 
        'expiration_date',
        'kilometer',
        'status'
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }

    public function shouldNotify() {
        $today = Carbon::now();
        return Carbon::parse($this->expiration_date)->diffInDays($today) <= 5;
    }
}
