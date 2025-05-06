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
        Schema::create('position_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('salary');
            $table->string('hire_date');
            $table->string('overtime_rate');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('hire_type_id');
            $table->foreign('hire_type_id')->references('id')->on('hire_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('position_id');
            $table->foreign('position_id')->references('id')->on('positions')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('position_change_type_id')->nullable();
            $table->foreign('position_change_type_id')->references('id')->on('position_change_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')
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
        Schema::dropIfExists('position_assignments');
    }
};
