<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Visitors can send a review from the home page. It is saved hidden (is_active = false) with source "visitor",
 * and stays "waiting" (reviewed_at null) until an admin shows it, hides it or edits it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('source', 20)->default('admin')->after('rating');
            $table->string('ip', 45)->nullable()->after('source');
            $table->timestamp('reviewed_at')->nullable()->after('ip');
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['source', 'ip', 'reviewed_at']);
        });
    }
};
