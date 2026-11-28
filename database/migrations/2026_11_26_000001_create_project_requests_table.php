<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** "Start a project" briefs sent from the Contact page, read in Admin > Project requests. */
    public function up(): void
    {
        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('phone', 11);           // always 01XXXXXXXXX (checked and normalised on submit)
            $table->string('email', 190)->nullable();
            $table->string('company', 160)->nullable();
            $table->json('services')->nullable();  // titles of the service categories ticked
            $table->string('budget', 40)->nullable();
            $table->string('timeline', 40)->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('new'); // new | contacted | closed
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_requests');
    }
};
