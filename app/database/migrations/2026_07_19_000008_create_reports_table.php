<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('reports', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reporter_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('target_type', 40);
            $table->unsignedBigInteger('target_id');
            $table->string('category', 60)->index();
            $table->text('note')->nullable();
            $table->string('status', 30)->default('open')->index();
            $table->foreignId('reviewer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('resolution', 80)->nullable();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
