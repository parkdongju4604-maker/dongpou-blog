<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500);
            $table->string('title', 500)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('referrer_domain', 255)->nullable();
            $table->string('referrer_type', 32)->default('direct'); // direct / search / social / referral
            $table->string('user_agent', 500)->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->default('desktop');
            $table->string('browser', 64)->nullable();
            $table->string('os', 64)->nullable();
            $table->string('ip_hash', 64)->nullable(); // SHA-256 of IP (privacy-safe)
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index('path');
            $table->index('referrer_domain');
            $table->index('device_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
