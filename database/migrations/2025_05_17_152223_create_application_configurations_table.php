<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('application_configurations', function (Blueprint $table) {
            $table->id();
            $table->longText('app_logo_base64')->nullable();
            $table->longText('report_logo_base64')->nullable();
            $table->time('attendance_check_in_time');
            $table->time('attendance_check_out_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_configurations');
    }
};
