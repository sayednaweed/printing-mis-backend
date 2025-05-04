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
        Schema::create('transecation_items', function (Blueprint $table) {
            $table->id();
            $table->string('paid_at');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->string('quantity');
            $table->integer('weight');

            $table->unsignedBigInteger('transecation_id');
            $table->foreign('transecation_id')->references('id')->on('transecations')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('werehouse_id');
            $table->foreign('werehouse_id')->references('id')->on('werehouses')
                ->onUpdate('cascade')
                ->onDelete('no action');

            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('materials')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('parties')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')->references('id')->on('sizes')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colors')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('transecation_behavoir_id');
            $table->foreign('transecation_behavoir_id')->references('id')->on('transecation_behavoirs')
                ->onUpdate('cascade')
                ->onDelete('no action');

            $table->unsignedBigInteger('returned_from_transecation_item');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transecation_items');
    }
};
