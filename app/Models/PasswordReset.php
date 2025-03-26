<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordReset extends Model
{

    use HasFactory;

    protected $fillable = ['email', 'otp', 'expiry_time'];

    // Optionally, you can define the table name if it doesn't follow Laravel's naming convention
    protected $table = 'password_resets';

}
