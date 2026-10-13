<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Submitted by the visitor
            $table->string('name', 120);
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->string('concern', 100);
            $table->date('preferred_date')->nullable();
            $table->string('mode', 20);
            $table->text('message')->nullable();

            // Managed in the admin panel
            $table->string('status', 20)->default('new')->index();
            $table->text('notes')->nullable();

            // Context
            $table->string('source', 50)->default('homepage_appointment');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();

            $table->index('created_at');
            $table->index(['phone', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
