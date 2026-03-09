<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // 토큰 레이블
            $table->string('token', 64)->unique();        // SHA-256 해시 (hex 64자)
            $table->string('token_prefix', 10);           // 식별용 prefix (dp_xxxxx)
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();  // null = 만료 없음
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
