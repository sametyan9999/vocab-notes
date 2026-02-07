<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_word', function (Blueprint $table) {
            $table->foreignId('word_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();

            // 同じタグを同じ単語に2回付けない
            $table->unique(['word_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_word');
    }
};