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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('api_key');
            $table->text('persona')->nullable();
            $table->string('tone')->nullable();
            $table->boolean('is_multi_language')->default(false);
            $table->boolean('is_auto_language')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_available')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'name']);
            $table->index('is_active');
            $table->index('is_available');
            $table->index(['is_active', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
