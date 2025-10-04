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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();
            $table->string('title');
            $table->string('author');
            $table->string('publisher');
            $table->integer('publication_year');
            $table->string('category');
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('available_stock')->default(0);
            $table->string('cover_image')->nullable();
            $table->enum('status', ['available', 'borrowed', 'damaged', 'lost'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
