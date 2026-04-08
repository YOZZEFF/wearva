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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL', 'XXL'])->nullable();
            $table->string('color', 50)->nullable();
            $table->decimal('price', 10, 2)->unsigned();
            $table->unsignedInteger('stock')->default(0);


            $table->timestamps();
            $table->unique(['product_id', 'size', 'color']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
