<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->string('author_name', 50);
            $table->string('author_email', 200)->nullable();
            $table->string('password_hash', 255)->nullable();   // 본인 삭제용
            $table->text('content');
            $table->string('ip_hash', 64);                      // SHA-256 IP
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_spam')->default(false);
            $table->unsignedTinyInteger('spam_score')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
