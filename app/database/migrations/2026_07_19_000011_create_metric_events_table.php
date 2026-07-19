<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('metric_events', static function (Blueprint $table): void {
            $table->id();
            $table->string('event_name', 120)->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['event_name', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_events');
    }
};
