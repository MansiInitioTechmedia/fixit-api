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
            $table->enum('status', ['upcoming', 'completed', 'cancelled'])->default('upcoming')->nullable();
            $table->timestamps();
        });

    }


    public function down() {

        Schema::dropIfExists('schedules');

    }

};

