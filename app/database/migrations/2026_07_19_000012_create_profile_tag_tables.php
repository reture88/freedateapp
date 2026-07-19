<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('profile_tag_categories', static function (Blueprint $table): void {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('label', 120);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('profile_tags', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profile_tag_category_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 100)->unique();
            $table->string('label', 120);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['profile_tag_category_id', 'sort_order']);
        });

        Schema::create('dating_profile_tags', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dating_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profile_tag_id')->constrained()->cascadeOnDelete();
            $table->string('visibility', 30)->default('public');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['dating_profile_id', 'profile_tag_id']);
            $table->index(['profile_tag_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dating_profile_tags');
        Schema::dropIfExists('profile_tags');
        Schema::dropIfExists('profile_tag_categories');
    }
};
