<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('profile_preferences', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dating_profile_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('preferred_age_min');
            $table->unsignedTinyInteger('preferred_age_max');
            $table->unsignedSmallInteger('preferred_distance_km');
            $table->json('preferred_genders');
            $table->json('preferred_orientations');
            $table->json('relationship_intents');
            $table->boolean('exclude_ai_wingman_enabled')->default(false)->index();
            $table->json('dealbreakers')->nullable();
            $table->json('soft_preferences')->nullable();
            $table->timestamps();

            $table->index(['preferred_age_min', 'preferred_age_max']);
            $table->index('preferred_distance_km');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_preferences');
    }
};
