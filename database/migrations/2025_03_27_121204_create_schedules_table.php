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
            $table->date('expiration_date');
            $table->enum('kilometer', ['1000', '2000', '5000', '10000', '20000', '50000'])->default('5000')->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available')->nullable();
            $table->timestamps();
        });

    }


    public function down() {

        Schema::dropIfExists('schedules');

    }

};

