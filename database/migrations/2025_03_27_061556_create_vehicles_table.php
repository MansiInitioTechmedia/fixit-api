<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {

    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_number')->unique();
            $table->enum('vehicle_type', ['car', 'motorcycle', 'truck', 'bicycle', 'bus', 'van', 'suv'])->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
    
};
