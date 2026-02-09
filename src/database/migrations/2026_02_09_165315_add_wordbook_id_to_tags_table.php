<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->foreignId('wordbook_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete()
                ->after('id');

            // もし tags.name が unique の場合は、単語帳ごとに unique にしたいので外す
            // （環境により名前が違うので後で説明）
        });
    }

    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['wordbook_id']);
            $table->dropColumn('wordbook_id');
        });
    }
};