<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inbound_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mailbox_id')->constrained('directory_mailboxes')->cascadeOnDelete();
            $table->string('message_id')->nullable()->index();
            $table->string('from_email')->index();
            $table->string('to_email')->index();
            $table->string('subject')->nullable();
            $table->longText('text_body')->nullable();
            $table->longText('html_body')->nullable();
            $table->json('headers')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('received_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_messages');
    }
};
