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
        Schema::create('reciepts', function (Blueprint $table) {
            $table->id();
            $table->string('paid_at');
            $table->decimal('amount_paid', 15, 2);
            $table->string('receipt_number');

            $table->unsignedBigInteger('transecation_payment_id');
            $table->foreign('transecation_payment_id')->references('id')->on('transecation_payments')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('issued_by');
            $table->foreign('issued_by')->references('id')->on('users')
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
        Schema::dropIfExists('reciepts');
    }
};
