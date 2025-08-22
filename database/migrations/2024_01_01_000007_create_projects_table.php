<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('description');
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable(); // Array of image paths
            $table->json('technologies')->nullable(); // Array of technologies used
            $table->string('project_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->enum('status', ['planning', 'in_progress', 'completed', 'maintenance'])->default('completed');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_published', 'is_featured']);
            $table->index(['status', 'sort_order']);
            $table->fullText(['title', 'short_description', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
