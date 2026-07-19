<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('dating_profiles', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name', 80);
            $table->date('birthdate');
            $table->string('gender', 40);
            $table->string('orientation', 40);
            $table->string('city', 120);
            $table->char('country_code', 2);
            $table->double('approximate_latitude', 9, 6)->nullable();
            $table->double('approximate_longitude', 9, 6)->nullable();
            $table->string('relationship_intent', 60);
            $table->text('bio')->nullable();
            $table->string('visibility_status', 30)->default('incomplete')->index();
            $table->string('moderation_status', 30)->default('pending')->index();
            $table->boolean('ai_wingman_enabled')->default(false);
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamps();

            $table->index(['country_code', 'city']);
            $table->index(['gender', 'orientation']);
            $table->index(['relationship_intent', 'visibility_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dating_profiles');
    }
};
