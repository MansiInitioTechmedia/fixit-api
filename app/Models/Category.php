<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Category extends Model {

    use HasFactory;
    
    protected $fillable = [
        'name',
        'icon',
        'status',
    ];

    public function schedules() {
        return $this->hasMany(Schedule::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}

