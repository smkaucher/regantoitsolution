<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Service booking was removed: visitors now book by WhatsApp, email or phone call.
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('service_requests');
    }

    public function down(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('budget')->nullable();
            $table->text('message');
            $table->enum('status', ['new', 'contacted', 'closed'])->default('new');
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }
};
