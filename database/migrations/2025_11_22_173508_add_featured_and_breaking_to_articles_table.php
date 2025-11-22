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
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_published');
            $table->boolean('is_breaking')->default(false)->after('is_featured');
            $table->text('gallery_images')->nullable()->after('image_url'); // JSON array of image URLs
            $table->integer('reading_time')->nullable()->after('content'); // Reading time in minutes
            
            $table->index('is_featured');
            $table->index('is_breaking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['is_breaking']);
            $table->dropColumn(['is_featured', 'is_breaking', 'gallery_images', 'reading_time']);
        });
    }
};
