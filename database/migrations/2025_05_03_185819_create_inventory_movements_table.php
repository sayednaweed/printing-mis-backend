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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('description');

            $table->unsignedBigInteger('movement_type_id');
            $table->foreign('movement_type_id')->references('id')->on('movement_types')
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
            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colors')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('parties')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('mover_type_name');
            $table->foreign('mover_type_name')->references('name')->on('mover_types')
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
        Schema::dropIfExists('inventory_movements');
    }
};
