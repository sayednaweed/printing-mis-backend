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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('quantity');
            $table->string('description');
            $table->integer('weight');
            $table->string('manufacture_date')->nullable();
            $table->integer('expire_date')->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('werehouses')
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
            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')->references('id')->on('sizes')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('parties')
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
        Schema::dropIfExists('inventories');
    }
};
