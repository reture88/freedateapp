<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('blocks', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blocker_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason_category', 60)->nullable()->index();
            $table->timestamps();

            $table->unique(['blocker_user_id', 'blocked_user_id']);
            $table->index(['blocked_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
