<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cashier_id')->constrained('users');
            $table->decimal('opening_cash', 10, 2);
            $table->decimal('closing_cash', 10, 2)->nullable();
            $table->decimal('expected_cash', 10, 2)->nullable();
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->decimal('total_returns', 10, 2)->default(0);
            $table->decimal('total_cash', 10, 2)->default(0);
            $table->decimal('total_visa', 10, 2)->default(0);
            $table->decimal('total_instapay', 10, 2)->default(0);
            $table->decimal('total_wallet', 10, 2)->default(0);
            $table->integer('transaction_count')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_open')->default(true);
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
        });

        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_number')->unique();
            $table->foreignUuid('session_id')->constrained('pos_sessions');
            $table->foreignUuid('cashier_id')->constrained('users');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['COD', 'PAYMOB', 'STRIPE', 'CASH', 'VISA', 'INSTAPAY', 'WALLET']);
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->enum('status', ['COMPLETED', 'SUSPENDED', 'RETURNED', 'VOIDED'])->default('COMPLETED');
            $table->foreignUuid('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('return_reason')->nullable();
            $table->string('original_transaction_id')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_id')->constrained('pos_transactions')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products');
            $table->foreignUuid('variant_id')->constrained('product_variants');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('variant_id')->constrained('product_variants');
            $table->enum('action', ['SALE', 'RETURN', 'MANUAL_ADJUSTMENT', 'NEW_STOCK', 'DAMAGED', 'TRANSFER']);
            $table->integer('quantity');
            $table->integer('previous_qty');
            $table->integer('new_qty');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->uuid('user_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('pos_transaction_items');
        Schema::dropIfExists('pos_transactions');
        Schema::dropIfExists('pos_sessions');
    }
};
