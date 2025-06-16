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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_id');
            $table->date('payment_date');
            $table->decimal('net_salary', 15, 2)->comment('Added to ease reporting and tracking');
            $table->decimal('gross_salary', 15, 2)->comment('Added to ease reporting and tracking');
            $table->unsignedBigInteger('position_assignment_id');
            $table->foreign('position_assignment_id')->references('id')->on('position_assignments')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('accounts')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->text('detail')->nullable();
            $table->boolean('is_complete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
