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
        Schema::create('transaction_behavoir_trans', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->unsignedBigInteger('transaction_behavoir_id');
            $table->foreign('transaction_behavoir_id')->references('id')->on('transaction_behavoirs')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')
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
        Schema::dropIfExists('transaction_behavoir_trans');
    }
};
