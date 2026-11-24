<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Sessions and cache use files and there is no queue or password reset, so these tables are dead weight.
    public function up(): void
    {
        foreach (['cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens', 'sessions'] as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        //
    }
};
