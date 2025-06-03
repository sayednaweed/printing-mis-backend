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
        Schema::create('transaction_movements', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_date');
            $table->string('delivery_notes');
            $table->integer('delivery_status');
            $table->unsignedBigInteger('werehouse_id');
            $table->foreign('werehouse_id')->references('id')->on('werehouses')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('transaction_item_id');
            $table->foreign('transaction_item_id')->references('id')->on('transaction_items')
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
        Schema::dropIfExists('transaction_movements');
    }
};
