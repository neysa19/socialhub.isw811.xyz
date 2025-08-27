<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('image_path')->nullable(); // si adjuntas imagen local

            // instant | queue | scheduled
            $table->string('mode')->default('instant');

            // Para "scheduled" o para calcular orden en cola
            $table->timestamp('scheduled_at')->nullable();

            // draft | pending | running | done | failed | cancelled
            $table->string('status')->default('pending');

            $table->json('meta')->nullable(); // extras si hiciera falta
            $table->timestamps();
        });

        // Un objetivo por red social
        Schema::create('post_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // 'twitter','linkedin', etc.
            $table->string('provider_post_id')->nullable();
            $table->string('status')->default('pending'); // pending|queued|running|done|failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // Opcional: índice para disparar más rápido por fecha
        Schema::table('publications', function (Blueprint $table) {
            $table->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_targets');
        Schema::dropIfExists('publications');
    }
};