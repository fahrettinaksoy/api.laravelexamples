<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_brand', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('brand_id');
            $table->uuid('uuid')->unique();
            $table->string('code', 100)->unique();
            $table->string('image', 500)->nullable();
            $table->string('logo', 500)->nullable();
            $table->unsignedInteger('layout_id')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['is_active', 'sort_order']);
        });
        
        Schema::create('cat_brand_translation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('brand_translation_id');
            $table->unsignedBigInteger('brand_id')->comment('İlişkili tablo kimliği');
            $table->char('language_code', 5);
            $table->string('name', 255)->index();
            $table->string('slug', 300)->unique();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('tag', 1000)->nullable();
            $table->string('keyword', 1000)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keyword', 500)->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['brand_id', 'language_code'], 'idx_brand_lang_unique');
            $table->fullText(['name', 'description', 'tag', 'keyword']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_brand_translation');
        Schema::dropIfExists('cat_brand');
    }
};
