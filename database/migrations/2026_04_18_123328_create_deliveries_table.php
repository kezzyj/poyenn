<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_agent_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('status', [
                'assigned',
                'picked_up',
                'in_transit',
                'delivered',
                'failed'
            ])->default('assigned');

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->text('failure_reason')->nullable();
            $table->text('agent_notes')->nullable();
            $table->string('proof_of_delivery')->nullable();

            $table->decimal('agent_commission', 10, 2)->nullable();

            $table->timestamps();

            $table->index('order_id');
            $table->index('delivery_agent_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};