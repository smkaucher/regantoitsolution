<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
        });
        // Allow decimal stats such as the 4.9 rating.
        Schema::table('stats', function (Blueprint $table) {
            $table->decimal('value', 8, 1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('stats', function (Blueprint $table) {
            $table->unsignedInteger('value')->change();
        });
        Schema::table('service_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
