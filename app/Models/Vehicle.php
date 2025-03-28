<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'registration_number', 
        'vehicle_type', 
        'status'
    ];

    protected $attributes = [
        'vehicle_type' => 'car',
        'status' => 'available',
    ];
    

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
