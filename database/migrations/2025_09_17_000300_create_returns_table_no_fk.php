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
        if (!Schema::hasTable('returns')) {
            Schema::create('returns', function (Blueprint $table) {
                $table->id();
                $table->string('return_code')->unique();
                $table->unsignedBigInteger('borrow_id');
                $table->unsignedBigInteger('member_id');
                $table->unsignedBigInteger('book_id');
                $table->date('return_date');
                $table->integer('days_late')->default(0);
                $table->decimal('fine_amount', 10, 2)->default(0);
                $table->enum('book_condition', ['good', 'damaged', 'lost'])->default('good');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};


