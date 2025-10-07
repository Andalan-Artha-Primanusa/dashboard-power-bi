<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USERS (terintegrasi langsung dgn divisions & sites)
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Org & Site (FK langsung di sini)
            $table->foreignUuid('division_id')->nullable()
                  ->constrained('divisions')->nullOnDelete();

            $table->foreignUuid('default_site_id')->nullable()
                  ->constrained('sites')->nullOnDelete();

            $table->json('allowed_site_ids')->nullable(); // cast di Model -> array

            // Identity
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role', 50)->nullable()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // PASSWORD RESET TOKENS
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index(); // match UUID users.id
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->unsignedInteger('last_activity')->index();
        });

        // PIVOT AKSES SITE <-> USER (opsi kontrol akses granular)
        if (!Schema::hasTable('site_user')) {
            Schema::create('site_user', function (Blueprint $table) {
                $table->uuid('site_id');
                $table->uuid('user_id');
                $table->timestamps();

                $table->primary(['site_id','user_id']);
                $table->index(['user_id','site_id']);

                $table->foreign('site_id')->references('id')->on('sites')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_user');            // drop pivot dulu
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
