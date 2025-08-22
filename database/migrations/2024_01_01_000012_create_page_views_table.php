<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45);
            $table->string('referrer')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable();
            $table->timestamp('visited_at');
            $table->timestamps();

            // Indexes for analytics queries
            $table->index(['url', 'visited_at']);
            $table->index(['ip_address', 'visited_at']);
            $table->index('visited_at');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_views');
    }
};
