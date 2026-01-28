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

    private const TABLE_PAGE = 'cont_page';
    private const TABLE_PAGE_TRANSLATION = 'cont_page_translation';
    private const TABLE_PAGE_IMAGE = 'cont_page_image';
    
    private const TABLE_BLOG_POST = 'cont_blog_post';
    private const TABLE_BLOG_POST_TRANSLATION = 'cont_blog_post_translation';
    private const TABLE_BLOG_POST_CATEGORY = 'cont_blog_post_category';
    private const TABLE_BLOG_POST_IMAGE = 'cont_blog_post_image';
    private const TABLE_BLOG_POST_RELATED = 'cont_blog_post_related';
    
    private const TABLE_BLOG_COMMENT = 'cont_blog_comment';

    public function up(): void
    {
        Schema::create(self::TABLE_PAGE, function (Blueprint $table) {
            $table->comment('Statik sayfalar (Hakkımızda, Gizlilik Politikası vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('page_id')->comment('Sayfa benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Benzersiz kod');
            $table->string('image', 500)->nullable()->comment('Kapak görseli');
            $table->string('html', 255)->nullable()->comment('Özel şablon dosyası');
            $table->unsignedInteger('viewed')->default(0)->comment('Görüntülenme sayısı');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->unsignedInteger('layout_id')->default(0)->comment('Özel düzen kimliği');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektirir mi?');
            $table->boolean('is_legal')->default(false)->comment('Yasal sayfa mı? (Sözleşme vb.)');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_page_soft_delete');
            $table->index(['is_legal', 'status'], 'idx_page_legal_status');
        });

        Schema::create(self::TABLE_PAGE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Sayfa çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('page_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('page_id')->comment('Sayfa kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Sayfa başlığı');
            $table->string('slug', 300)->unique()->comment('SEO dostu URL');
            $table->string('summary', 500)->nullable()->comment('Özet');
            $table->longText('description')->nullable()->comment('İçerik');
            $table->string('tag', 500)->nullable()->comment('Etiketler');
            $table->string('keyword', 255)->nullable()->comment('SEO Slug');
            $table->string('meta_title', 255)->nullable()->comment('SEO başlık');
            $table->string('meta_description', 500)->nullable()->comment('SEO açıklama');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO anahtar kelimeler');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['page_id', 'language_code'], 'idx_page_lang_unique');
            $table->fullText(['name', 'description'], 'idx_page_fulltext');
            $table->index('deleted_at', 'idx_page_trans_soft_delete');
        });

        Schema::create(self::TABLE_PAGE_IMAGE, function (Blueprint $table) {
            $table->comment('Sayfa galeri görselleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('page_image_id')->comment('Görsel benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('page_id')->index()->comment('Sayfa kimliği');
            $table->string('file', 500)->comment('Dosya yolu');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
        });

        Schema::create(self::TABLE_BLOG_POST, function (Blueprint $table) {
            $table->comment('Blog yazıları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('post_id')->comment('Yazı benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Benzersiz kod');
            $table->string('image', 500)->nullable()->comment('Kapak görseli');
            $table->unsignedInteger('viewed')->default(0)->comment('Okunma sayısı');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->unsignedInteger('layout_id')->default(0)->comment('Özel düzen');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektirir mi?');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_post_soft_delete');
        });

        Schema::create(self::TABLE_BLOG_POST_TRANSLATION, function (Blueprint $table) {
            $table->comment('Blog yazısı çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('post_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('post_id')->comment('Yazı kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Yazı başlığı');
            $table->string('slug', 300)->unique()->comment('SEO dostu URL');
            $table->string('summary', 500)->nullable()->comment('Özet');
            $table->longText('description')->nullable()->comment('İçerik');
            $table->string('tag', 500)->nullable()->comment('Etiketler');
            $table->string('keyword', 255)->nullable()->comment('SEO Slug');
            $table->string('meta_title', 255)->nullable()->comment('SEO başlık');
            $table->string('meta_description', 500)->nullable()->comment('SEO açıklama');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO anahtar kelimeler');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['post_id', 'language_code'], 'idx_post_lang_unique');
            $table->fullText(['name', 'description'], 'idx_post_fulltext');
            $table->index('deleted_at', 'idx_post_trans_soft_delete');
        });

        Schema::create(self::TABLE_BLOG_POST_CATEGORY, function (Blueprint $table) {
            $table->comment('Blog yazısı - Kategori ilişkisi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('post_category_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('post_id')->comment('Yazı kimliği');
            $table->unsignedBigInteger('category_id')->comment('Kategori kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');

            $table->unique(['post_id', 'category_id'], 'idx_post_cat_unique');
        });

        Schema::create(self::TABLE_BLOG_POST_IMAGE, function (Blueprint $table) {
            $table->comment('Blog yazısı galeri görselleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('post_image_id')->comment('Görsel benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('post_id')->index()->comment('Yazı kimliği');
            $table->string('file', 500)->comment('Dosya yolu');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
        });

        Schema::create(self::TABLE_BLOG_POST_RELATED, function (Blueprint $table) {
            $table->comment('İlişkili blog yazıları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('post_related_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('post_id')->comment('Ana yazı');
            $table->unsignedBigInteger('related_id')->comment('İlişkili yazı');
            
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            
            $table->unique(['post_id', 'related_id'], 'idx_post_rel_unique');
        });

        Schema::create(self::TABLE_BLOG_COMMENT, function (Blueprint $table) {
            $table->comment('Blog yorumları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('comment_id')->comment('Yorum benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('post_id')->index()->comment('Yazı kimliği');
            $table->unsignedBigInteger('account_id')->default(0)->index()->comment('Üye kimliği (0 ise misafir)');
            $table->string('author', 100)->comment('Yorum yapan isim');
            $table->string('email', 100)->nullable()->comment('E-posta');
            $table->text('content')->comment('Yorum içeriği');
            $table->tinyInteger('rating')->default(0)->comment('Puan (0-5)');
            $table->boolean('status')->default(false)->index()->comment('Onay durumu');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_comment_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_BLOG_COMMENT);
        Schema::dropIfExists(self::TABLE_BLOG_POST_RELATED);
        Schema::dropIfExists(self::TABLE_BLOG_POST_IMAGE);
        Schema::dropIfExists(self::TABLE_BLOG_POST_CATEGORY);
        Schema::dropIfExists(self::TABLE_BLOG_POST_TRANSLATION);
        Schema::dropIfExists(self::TABLE_BLOG_POST);
        Schema::dropIfExists(self::TABLE_PAGE_IMAGE);
        Schema::dropIfExists(self::TABLE_PAGE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PAGE);
    }
};
