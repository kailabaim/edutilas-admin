<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'category')) {
                $table->string('category')->default('Umum');
            }
            if (!Schema::hasColumn('books', 'publication_year')) {
                $table->integer('publication_year')->nullable();
            }
            if (!Schema::hasColumn('books', 'stock')) {
                $table->integer('stock')->default(0);
            }
            if (!Schema::hasColumn('books', 'available_stock')) {
                $table->integer('available_stock')->default(0);
            }
            if (!Schema::hasColumn('books', 'status')) {
                $table->enum('status', ['available', 'borrowed', 'damaged', 'lost'])->default('available');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'category')) $table->dropColumn('category');
            if (Schema::hasColumn('books', 'publication_year')) $table->dropColumn('publication_year');
            if (Schema::hasColumn('books', 'stock')) $table->dropColumn('stock');
            if (Schema::hasColumn('books', 'available_stock')) $table->dropColumn('available_stock');
            if (Schema::hasColumn('books', 'status')) $table->dropColumn('status');
        });
    }
};


