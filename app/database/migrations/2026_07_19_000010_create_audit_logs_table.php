<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 120)->index();
            $table->string('target_type', 40);
            $table->unsignedBigInteger('target_id');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
            $table->index(['actor_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
