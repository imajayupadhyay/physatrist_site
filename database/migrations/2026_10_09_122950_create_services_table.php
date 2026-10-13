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
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Basics
            $table->string('title', 120);
            $table->string('slug', 100)->unique();
            $table->string('concern_label', 100)->nullable(); // name in the appointment form
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            // Homepage card
            $table->boolean('show_on_home')->default(true);
            $table->boolean('is_featured')->default(false); // dark highlighted card
            $table->string('icon', 40)->default('leaf');
            $table->string('audience', 80)->nullable(); // "Teens · Adults"
            $table->string('summary', 300);

            // Detail page
            $table->string('hero_eyebrow', 80)->nullable();
            $table->string('hero_title', 150);
            $table->text('intro');
            $table->string('hero_image')->nullable();
            $table->string('overview_heading', 150)->nullable();
            $table->text('overview_body')->nullable();
            $table->string('issues_heading', 150)->nullable();
            $table->json('issues')->nullable();
            $table->string('approach_heading', 150)->nullable();
            $table->json('approach')->nullable();
            $table->string('cta_heading', 150)->nullable();
            $table->string('cta_text', 400)->nullable();
            $table->json('faqs')->nullable();

            // SEO
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description', 320)->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);

            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
