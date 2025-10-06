<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('powerbi_reports', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('name');
            $t->text('embed_url');
            $t->boolean('show_filter_pane')->default(false);
            $t->boolean('show_nav_pane')->default(true);
            $t->boolean('show_toolbar')->default(true);
            $t->boolean('allow_client_download')->default(true);
            $t->uuid('created_by')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('powerbi_report_user', function (Blueprint $t) {
            $t->uuid('report_id');
            $t->uuid('user_id');
            $t->timestamps();
            $t->primary(['report_id','user_id']);
            $t->foreign('report_id')->references('id')->on('powerbi_reports')->cascadeOnDelete();
            $t->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('powerbi_report_division', function (Blueprint $t) {
            $t->uuid('report_id');
            $t->uuid('division_id');
            $t->timestamps();
            $t->primary(['report_id','division_id']);
            $t->foreign('report_id')->references('id')->on('powerbi_reports')->cascadeOnDelete();
            $t->foreign('division_id')->references('id')->on('divisions')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('powerbi_report_division');
        Schema::dropIfExists('powerbi_report_user');
        Schema::dropIfExists('powerbi_reports');
    }
};
