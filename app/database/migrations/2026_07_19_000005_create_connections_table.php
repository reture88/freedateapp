<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('connections', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profile_one_id')->constrained('dating_profiles')->cascadeOnDelete();
            $table->foreignId('profile_two_id')->constrained('dating_profiles')->cascadeOnDelete();
            $table->string('profile_one_decision', 30)->default('pending');
            $table->string('profile_two_decision', 30)->default('pending');
            $table->string('status', 30)->default('pending')->index();
            $table->timestamp('matched_at')->nullable()->index();
            $table->timestamp('unmatched_at')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamps();

            $table->unique(['profile_one_id', 'profile_two_id']);
            $table->index(['profile_one_id', 'status']);
            $table->index(['profile_two_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
