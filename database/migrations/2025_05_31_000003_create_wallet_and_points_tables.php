<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wallet: stores user balance
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->decimal('balance', 10, 2)->default(0);
            $table->integer('points')->default(0);
            $table->timestamps();
        });

        // Wallet transactions log
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->enum('type', ['CREDIT', 'DEBIT']);
            $table->decimal('amount', 10, 2)->default(0);
            $table->integer('points')->default(0);
            $table->string('description');
            $table->string('reference_type')->nullable(); // Order, Refund, Admin, Points, Signup
            $table->string('reference_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
