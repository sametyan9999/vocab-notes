<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('words', function (Blueprint $table) {
            $table->foreignId('wordbook_id')
                  ->nullable()               // いま既存データがあるから一旦nullable
                  ->constrained()             // wordbooks.id と紐づく
                  ->nullOnDelete()            // シート削除時はnullに
                  ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('words', function (Blueprint $table) {
            $table->dropForeign(['wordbook_id']);
            $table->dropColumn('wordbook_id');
        });
    }
};