<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'registration_number', 
        'vehicle_type', 
        'status',
        'user_id'
    ];

    protected $attributes = [
        'vehicle_type' => 'car',
        'status' => 'available',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vehicle) {
            if (empty($vehicle->status)) {
                $vehicle->status = 'available';
            }
        });
    }
}
