<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_BLOG_CATEGORY = 'def_cont_blog_category';
    private const TABLE_BLOG_CATEGORY_TRANSLATION = 'def_cont_blog_category_translation';

    public function up(): void
    {
        Schema::create(self::TABLE_BLOG_CATEGORY, function (Blueprint $table) {
            $table->comment('Blog kategori tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('category_id')->comment('Kategori benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('parent_id')->default(0)->index()->comment('Üst kategori kimliği');
            $table->string('code', 100)->unique()->comment('Benzersiz kod');
            $table->string('image', 500)->nullable()->comment('Görsel URL');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektirir mi?');
            $table->unsignedInteger('layout_id')->default(0)->comment('Özel sayfa düzeni kimliği');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_blog_cat_soft_delete');
            $table->index(['parent_id', 'sort_order'], 'idx_blog_cat_parent_sort');
            $table->index(['status', 'sort_order'], 'idx_blog_cat_status_sort');
            $table->index('layout_id', 'idx_blog_cat_layout');
            $table->index('requires_membership', 'idx_blog_cat_membership');
        });

        Schema::create(self::TABLE_BLOG_CATEGORY_TRANSLATION, function (Blueprint $table) {
            $table->comment('Blog kategori çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('category_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('category_id')->comment('Kategori kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Kategori adı');
            $table->string('summary', 500)->nullable()->comment('Özet');
            $table->longText('description')->nullable()->comment('Açıklama');
            $table->string('keyword', 255)->nullable()->comment('SEO Slug');
            $table->string('meta_title', 255)->nullable()->comment('SEO başlık');
            $table->string('meta_description', 500)->nullable()->comment('SEO açıklama');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO anahtar kelimeler');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['category_id', 'language_code'], 'idx_blog_cat_lang_unique');
            $table->index('keyword', 'idx_blog_cat_trans_keyword');
            $table->fullText(['name', 'description', 'summary'], 'idx_blog_cat_trans_fulltext');
            $table->index('deleted_at', 'idx_blog_cat_trans_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_BLOG_CATEGORY_TRANSLATION);
        Schema::dropIfExists(self::TABLE_BLOG_CATEGORY);
    }
};
