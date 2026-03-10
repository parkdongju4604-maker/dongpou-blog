<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['useragent', 'ip']);
            $table->string('value', 500);
            $table->boolean('is_active')->default(true);
            $table->string('note', 200)->nullable();
            $table->timestamps();
            $table->index(['type', 'is_active']);
        });

        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45);
            $table->string('user_agent', 500)->nullable()->default('');
            $table->unsignedBigInteger('count')->default(1);
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();
            $table->unique(['ip', 'user_agent'], 'al_ip_ua_unique');
            $table->index('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
        Schema::dropIfExists('block_rules');
    }
};
