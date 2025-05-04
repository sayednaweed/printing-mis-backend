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
        Schema::create('transecation_movements', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_date');
            $table->string('delivery_notes');
            $table->integer('delivery_status');
            $table->unsignedBigInteger('werehouse_id');
            $table->foreign('werehouse_id')->references('id')->on('werehouses')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('transecation_item_id');
            $table->foreign('transecation_item_id')->references('id')->on('transecation_items')
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
        Schema::dropIfExists('transecation_movements');
    }
};
