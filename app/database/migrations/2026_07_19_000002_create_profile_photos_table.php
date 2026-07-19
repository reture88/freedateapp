<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('profile_photos', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dating_profile_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->string('moderation_status', 30)->default('pending')->index();
            $table->timestamps();

            $table->index(['dating_profile_id', 'sort_order']);
            $table->index(['dating_profile_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_photos');
    }
};
