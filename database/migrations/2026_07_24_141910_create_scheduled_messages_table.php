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
        Schema::create('scheduled_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('topic');
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->json('languages')->nullable();
            $table->enum('schedule_type', [
                'recurring',
                'specific_dates',
            ]);
            $table->date('start_date')->nullable();
            $table->string('frequency')->nullable();
            $table->time('run_at')->nullable();
            $table->string('ends_type')->default('never');
            $table->date('ends_on')->nullable();
            $table->unsignedInteger('sent_count')->default(0);
            $table->json('run_dates')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamps();

            // indexing
            $table->index(['is_active', 'run_at']);
            $table->index('start_date');
            $table->index(['schedule_type', 'frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_messages');
    }
};
