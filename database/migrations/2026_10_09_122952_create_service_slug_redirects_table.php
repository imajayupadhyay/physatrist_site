<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Old service slugs that 301-redirect to the service's current URL.
     */
    public function up(): void
    {
        Schema::create('service_slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_slug', 100)->unique();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_slug_redirects');
    }
};
