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
        Schema::create('transaction_project_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('tran_movement_id');
            $table->foreign('tran_movement_id')->references('id')->on('transaction_movements')
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
        Schema::dropIfExists('transaction_project_movements');
    }
};
