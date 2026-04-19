<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mail_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('message_id')->nullable()->index();
            $table->string('recipient')->index();
            $table->string('subject');
            $table->string('template')->nullable();
            $table->string('status')->default('queued')->index();
            $table->string('provider')->default('smtp');
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
