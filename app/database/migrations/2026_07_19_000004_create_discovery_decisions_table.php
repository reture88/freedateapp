<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('discovery_decisions', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('viewer_profile_id')->constrained('dating_profiles')->cascadeOnDelete();
            $table->foreignId('target_profile_id')->constrained('dating_profiles')->cascadeOnDelete();
            $table->string('decision', 30);
            $table->timestamp('decided_at');
            $table->timestamps();

            $table->unique(['viewer_profile_id', 'target_profile_id']);
            $table->index(['target_profile_id', 'decision']);
            $table->index('decided_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discovery_decisions');
    }
};
