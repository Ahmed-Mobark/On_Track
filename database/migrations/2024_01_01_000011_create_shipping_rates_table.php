<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('governorate');
            $table->string('city')->nullable();
            $table->decimal('cost', 10, 2);
            $table->integer('estimated_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['governorate', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
