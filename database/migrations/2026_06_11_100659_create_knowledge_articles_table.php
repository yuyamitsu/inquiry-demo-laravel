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
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();

            // 元になった問い合わせ
            $table->foreignId('inquiry_id')
                ->nullable()
                ->constrained('inquiries')
                ->nullOnDelete();

            // ナレッジ作成者
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // ナレッジ記事の内容
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('body');

            // 公開状態
            // まずは社内用として使う想定。trueなら公開、falseなら下書き。
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index('title');
            $table->index('category');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_articles');
    }
};
