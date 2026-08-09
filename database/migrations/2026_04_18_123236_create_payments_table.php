<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->enum('payment_method', ['flutterwave', 'cash_on_delivery']);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('NGN');

            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('pending');

            // Flutterwave specific fields
            $table->string('flutterwave_ref')->nullable()->index();
            $table->string('flutterwave_tx_id')->nullable()->index();
            $table->string('flutterwave_payment_type')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};