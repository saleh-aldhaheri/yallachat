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
        Schema::create('chat_scheduled_message', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chat_id')->index()->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'pending',
                'sent',
                'failed',
                'overdue',
            ])->default('pending');
            $table->text('message')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_paused')->default(false);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique([
                'scheduled_message_id',
                'chat_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_scheduled_message');
    }
};
