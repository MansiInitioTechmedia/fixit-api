<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
        });
    }
};
