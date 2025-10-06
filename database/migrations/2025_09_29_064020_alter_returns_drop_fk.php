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
        Schema::table('returns', function (Blueprint $table) {
            // Beberapa instalasi menamai constraint otomatis, jadi kita handle dua kemungkinan
            try {
                $table->dropForeign('fk_returns_loan');
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['loan_id']);
            } catch (\Throwable $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            // Kembalikan FK jika perlu (optional, gunakan nama generik)
            if (!Schema::hasColumn('returns', 'loan_id')) {
                return;
            }
            try {
                $table->foreign('loan_id', 'fk_returns_loan')
                    ->references('loan_id')->on('loans')
                    ->onDelete('cascade');
            } catch (\Throwable $e) {}
        });
    }
};
