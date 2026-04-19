<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('directory_mailboxes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('directory_id')->nullable()->index();
            $table->string('name');
            $table->string('local_part');
            $table->string('domain')->default('pct.social.br');
            $table->string('email')->unique();
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->json('forward_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['local_part', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directory_mailboxes');
    }
};
