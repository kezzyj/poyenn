<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('channel', ['sms', 'email']);
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('message');

            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->enum('provider', ['termii', 'laravel_mail'])->nullable();
            $table->json('provider_response')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamps();

            $table->index(['channel', 'status']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};