<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Blog posts. Each post is written in one language (locale); posts are
     * not translated. A null published_at is a draft, a future one is
     * scheduled. body_html is the rendered Markdown, refreshed on save.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->longText('body_html');
            $table->string('locale', 8)->default('en');
            $table->json('tags')->nullable(); // not shown yet; for tag pages later
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
