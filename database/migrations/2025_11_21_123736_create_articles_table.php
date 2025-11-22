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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->text('summary');
            $table->string('url');
            $table->string('image_url')->nullable();
            $table->timestamp('published_at');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('source_id')->constrained();
            $table->string('language', 10)->default('en');
            $table->decimal('ai_score', 3, 2)->default(0);
            $table->boolean('is_ai_related')->default(false);
            $table->boolean('is_published')->default(false);
            $table->string('author')->nullable();
            $table->json('tags')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['is_published', 'published_at']);
            $table->index(['is_ai_related', 'ai_score']);
            $table->index(['language', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
