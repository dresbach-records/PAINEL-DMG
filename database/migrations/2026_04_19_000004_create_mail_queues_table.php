<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mail_queues', function (Blueprint $table): void {
            $table->id();
            $table->string('recipient')->index();
            $table->string('subject');
            $table->string('template');
            $table->json('payload')->nullable();
            $table->string('priority')->default('normal')->index();
            $table->string('status')->default('queued')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_queues');
    }
};
