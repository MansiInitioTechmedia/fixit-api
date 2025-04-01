<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {

    public function up() {

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('expiration_date')->nullable()->default(null);
            $table->integer('kilometers')->nullable()->default(null);
            $table->integer('status'); // Store status as integer (0, 1, 2, 3)
            $table->date('service_date'); // New column for storing the current date
            $table->timestamps();
        });

    }


    public function down() {

        Schema::dropIfExists('schedules');

    }

};

