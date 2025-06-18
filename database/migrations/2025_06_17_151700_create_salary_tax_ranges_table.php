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
        Schema::create('salary_tax_ranges', function (Blueprint $table) {
            $table->id();
            $table->decimal('start', 15, 2)->comment(' start of the salary range  ');
            $table->decimal('end', 15, 2)->comment('End of the  salary range');
            $table->decimal('fixed_tax', 15, 2)->nullable()->comment('fix tax');
            $table->decimal('percentage_tax', 5, 2)->nullable()->comment('The tax of this range in persentage');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_tax_ranges');
    }
};
