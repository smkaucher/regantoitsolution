<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The team is now static frontend data (config/team.php), so the tables are no longer needed.
    public function up(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('team_categories');
    }

    public function down(): void
    {
        //
    }
};
