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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->string('issued_at');
            $table->unsignedBigInteger('issued_by');
            $table->foreign('issued_by')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')
                ->onUpdate('cascade')
                ->onDelete('no action');

            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')
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
        Schema::dropIfExists('invoices');
    }
};
