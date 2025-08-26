<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            // si no existe, agrégala después de refresh_token
            if (!Schema::hasColumn('social_accounts', 'token_expires_at')) {
                $table->timestamp('token_expires_at')->nullable()->after('refresh_token');
            }

            // por si también faltan:
            if (!Schema::hasColumn('social_accounts', 'scopes')) {
                $table->text('scopes')->nullable()->after('token_expires_at');
            }
            if (!Schema::hasColumn('social_accounts', 'meta')) {
                // usa JSON si tu MySQL lo soporta; si no, text()
                $table->json('meta')->nullable()->after('scopes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropColumn(['token_expires_at', 'scopes', 'meta']);
        });
    }
};
