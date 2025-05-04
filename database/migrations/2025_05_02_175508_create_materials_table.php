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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->unsignedBigInteger('icon_id');
            $table->foreign('icon_id')->references('id')->on('icons')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('material_catagory_id');
            $table->foreign('material_catagory_id')->references('id')->on('material_catagories')
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
        Schema::dropIfExists('materials');
    }
};
