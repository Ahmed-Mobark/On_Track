<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('address_id')->constrained('addresses');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED', 'RETURNED'])->default('PENDING');
            $table->enum('payment_method', ['COD', 'PAYMOB', 'STRIPE', 'CASH', 'VISA', 'INSTAPAY', 'WALLET']);
            $table->enum('payment_status', ['PENDING', 'PAID', 'FAILED', 'REFUNDED'])->default('PENDING');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->foreignUuid('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('tracking_number')->nullable();
            $table->enum('shipment_status', ['AWAITING_PICKUP', 'PICKED_UP', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'DELIVERY_FAILED', 'RETURNED'])->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products');
            $table->foreignUuid('variant_id')->constrained('product_variants');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
