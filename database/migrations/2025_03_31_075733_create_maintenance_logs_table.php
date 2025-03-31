<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {

    public function up(): void
    {

        Schema::create('maintenance_logs', function (Blueprint $table) {

            $table->id();
            $table->string('car_name');
            $table->string('service_type');
            $table->date('maintenance_date'); // using maintenance_date to store the date
            $table->decimal('amount', 10, 2);
            $table->json('receipts')->nullable(); // store receipt file paths as JSON
            $table->timestamps();
        });

    }


    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
    
};
