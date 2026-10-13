<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Website header menu (two levels: top-level items and their dropdown items).
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('type', 20)->default('link'); // link | service
            $table->foreignId('service_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('label', 60)->nullable(); // optional for service items (defaults to the service title)
            $table->string('url')->nullable(); // for link items
            $table->string('description', 160)->nullable(); // dropdown subtitle
            $table->string('dropdown_style', 10)->default('simple'); // simple | mega (top-level items with children)
            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
