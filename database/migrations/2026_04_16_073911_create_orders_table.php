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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('address_id')->constrained('addresses')->restrictOnDelete();
            $table->enum('status',['pending','confirmed','processing','shipped','delivered','cancelled'])->default('pending');
            $table->decimal('sub_total',10,2);
            $table->decimal('discount' ,10,2)->default(0);
            $table->decimal('shipping_cost',10,2)->default(0);
            $table->decimal('total',10,2);
            $table->enum('payment_method', ['cash_on_delivery', 'credit_card'])->default('cash_on_delivery');
            $table->enum('payment_status',['pending','paid','failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
