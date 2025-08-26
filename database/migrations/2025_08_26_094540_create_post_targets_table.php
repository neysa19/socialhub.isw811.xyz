<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_targets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('publication_id')->constrained('publications')->cascadeOnDelete();
            $t->string('provider');                 // twitter | linkedin | ...
            $t->string('status')->default('pending'); // pending|processing|posted|failed
            $t->string('provider_post_id')->nullable();
            $t->text('error')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_targets');
    }
};
