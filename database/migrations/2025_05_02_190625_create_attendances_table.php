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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('check_in_time')->nullable();
            $table->string('check_out_time')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('taken_by_id');
            $table->foreign('taken_by_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('attendance_status_id');
            $table->foreign('attendance_status_id')->references('id')->on('attendance_statuses')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
