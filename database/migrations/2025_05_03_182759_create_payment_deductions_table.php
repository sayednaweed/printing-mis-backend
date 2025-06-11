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
        Schema::create('payment_deductions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('deduction_type_id');
            $table->foreign('deduction_type_id')->references('id')->on('deduction_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
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
        Schema::dropIfExists('payment_deductions');
    }
};
