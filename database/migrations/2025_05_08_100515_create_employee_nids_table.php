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
        Schema::create('employee_nids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nid_type_id');
            $table->foreign('nid_type_id')->references('id')->on('nid_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('register_number');
            $table->string('register');
            $table->string('volume')->nullable();
            $table->string('page')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_nids');
    }
};
