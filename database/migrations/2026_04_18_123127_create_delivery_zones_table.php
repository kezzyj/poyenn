<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('state');
            $table->text('covered_cities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['platform_id', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};