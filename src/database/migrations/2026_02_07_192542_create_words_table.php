<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->string('term');              // 単語
            $table->string('meaning');           // 意味
            $table->text('note')->nullable();    // メモ（null OK）
            $table->timestamps();

            $table->index('term');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};