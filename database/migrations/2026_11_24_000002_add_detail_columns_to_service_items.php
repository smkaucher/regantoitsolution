<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Each service now has its own page: a slug, a picture and the list of things it includes.
    public function up(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->string('image')->nullable()->after('description');
            $table->json('points')->nullable()->after('image');
            $table->unique(['service_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->dropUnique(['service_id', 'slug']);
            $table->dropColumn(['slug', 'image', 'points']);
        });
    }
};
