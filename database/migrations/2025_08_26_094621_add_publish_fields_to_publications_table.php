<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $t) {
            if (!Schema::hasColumn('publications', 'title')) {
                $t->string('title')->nullable()->after('id');
            }

            // Si NO tienes la columna 'content', créala:
            if (!Schema::hasColumn('publications', 'content')) {
                $t->text('content')->nullable();
            }
            // Si sí existe y quieres volverla nullable, necesitas DBAL y descomentar:
            // composer require doctrine/dbal
            // $t->text('content')->nullable()->change();

            if (!Schema::hasColumn('publications', 'media_path')) {
                $t->string('media_path')->nullable();
            }
            if (!Schema::hasColumn('publications', 'mode')) {
                $t->enum('mode', ['instant','queued','scheduled'])->default('instant');
            }
            if (!Schema::hasColumn('publications', 'scheduled_at')) {
                $t->timestamp('scheduled_at')->nullable();
            }
            if (!Schema::hasColumn('publications', 'status')) {
                $t->string('status')->default('queued');
            }
            if (!Schema::hasColumn('publications', 'published_at')) {
                $t->timestamp('published_at')->nullable();
            }

            // Solo si ya existe user_id y quieres asegurar índice:
            // if (Schema::hasColumn('publications', 'user_id')) {
            //     $t->index('user_id');
            // }
            // (Si NO existe user_id, créalo con foreign key en otra migración)
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $t) {
            if (Schema::hasColumn('publications', 'title'))        $t->dropColumn('title');
            if (Schema::hasColumn('publications', 'media_path'))   $t->dropColumn('media_path');
            if (Schema::hasColumn('publications', 'mode'))         $t->dropColumn('mode');
            if (Schema::hasColumn('publications', 'scheduled_at')) $t->dropColumn('scheduled_at');
            if (Schema::hasColumn('publications', 'status'))       $t->dropColumn('status');
            if (Schema::hasColumn('publications', 'published_at')) $t->dropColumn('published_at');
        });
    }
};
