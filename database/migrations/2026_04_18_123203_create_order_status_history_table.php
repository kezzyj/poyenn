<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->text('note')->nullable();
            $table->enum('changed_by_type', ['admin', 'delivery_agent', 'customer', 'system'])->default('system');
            $table->unsignedBigInteger('changed_by_id')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index(['changed_by_type', 'changed_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
    }
};