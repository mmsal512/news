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
            $table->text('copyright_notice')->nullable()->after('meta_description');
            $table->string('attribution_text')->nullable()->after('copyright_notice');
            $table->boolean('requires_attribution')->default(true)->after('attribution_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['copyright_notice', 'attribution_text', 'requires_attribution']);
        });
    }
};
