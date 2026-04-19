<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mail_contacts', function (Blueprint $table): void {
            $table->id();
            $table->string('external_id')->nullable()->index();
            $table->string('type')->default('manual')->index(); // manual|afiliado|integrante
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('directory_id')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('source_updated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['email', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_contacts');
    }
};
