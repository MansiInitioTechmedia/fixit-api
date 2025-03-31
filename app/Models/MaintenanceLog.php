<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MaintenanceLog extends Model

{
    use HasFactory;


    protected $fillable = [
        'car_name',
        'service_type',
        'maintenance_date',
        'amount',
        'receipts'
    ];


    protected $casts = [
        'receipts' => 'array', // automatically cast the JSON receipts field to an array
    ];
    
}
