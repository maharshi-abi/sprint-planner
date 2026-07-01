<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('summary_date');
            $table->decimal('target_hours', 5, 2)->default(8);
            $table->decimal('completed_hours', 8, 2)->default(0);
            $table->decimal('remaining_hours', 8, 2)->default(8);
            $table->timestamps();

            $table->unique(['user_id', 'summary_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
