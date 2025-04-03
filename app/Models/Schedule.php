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
        'kilometers',
        'status',
        'service_date',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
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

//     public function scopeFilterByStatus($query, $status)
// {
//     return $query->when(isset($status), function ($query) use ($status) {
//         return $query->where('status', (int) $status); // ✅ Ensure integer comparison
//     });
// }

// public function scopeFilterByDate($query, $date)
// {
//     return $query->when($date, function ($query) use ($date) {
//         return $query->whereDate('service_date', $date);
//     });
// }

}
