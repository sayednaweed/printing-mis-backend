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
        Schema::create('payroll_overtimes', function (Blueprint $table) {
            $table->id();
            $table->decimal('overtime_rate', 15, 2);
            $table->decimal('overtime_hours', 15, 2);
            $table->unsignedBigInteger('payroll_payment_id');
            $table->foreign('payroll_payment_id')->references('id')->on('payroll_payments')
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
        Schema::dropIfExists('payroll_overtimes');
    }
};
