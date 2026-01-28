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

    private const TABLE_CATEGORY = 'def_app_category';
    private const TABLE_CATEGORY_TRANSLATION = 'def_app_category_translation';
    private const TABLE_APPLICATION = 'def_app_application';
    private const TABLE_APPLICATION_TRANSLATION = 'def_app_application_translation';
    private const TABLE_APPLICATION_IMAGE = 'def_app_application_image';
    private const TABLE_APPLICATION_RELATED = 'def_app_application_related';

    public function up(): void
    {
        Schema::create(self::TABLE_CATEGORY, function (Blueprint $table) {
            $table->comment('Uygulama kategorileri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('category_id')->comment('Kategori benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('parent_id')->default(0)->index()->comment('Üst kategori kimliği');
            $table->string('type', 50)->comment('Kategori tipi (addon, theme, integration vb.)');
            $table->string('code', 100)->unique()->comment('Benzersiz kod');
            $table->string('image', 500)->nullable()->comment('Görsel URL');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['type', 'parent_id', 'sort_order'], 'idx_app_cat_composite');
            $table->index('deleted_at', 'idx_app_cat_soft_delete');
        });

        Schema::create(self::TABLE_CATEGORY_TRANSLATION, function (Blueprint $table) {
            $table->comment('Kategori çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('category_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('category_id')->comment('Kategori kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Kategori adı');
            $table->string('slug', 300)->unique()->comment('SEO dostu URL');
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

            $table->unique(['category_id', 'language_code'], 'idx_app_cat_lang_unique');
            $table->index('deleted_at', 'idx_app_cat_trans_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION, function (Blueprint $table) {
            $table->comment('Uygulamalar ve eklentiler');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_id')->comment('Uygulama benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('category_id')->default(0)->index()->comment('Kategori kimliği');
            $table->unsignedBigInteger('developer_id')->default(0)->comment('Geliştirici kimliği');
            $table->string('type', 50)->comment('Uygulama tipi (free, paid, subscription)');
            $table->string('code', 100)->unique()->comment('Benzersiz kod');
            $table->string('icon', 500)->nullable()->comment('İkon');
            $table->string('cover', 500)->nullable()->comment('Kapak görseli');
            $table->string('banner', 500)->nullable()->comment('Banner görseli');
            $table->string('video', 500)->nullable()->comment('Tanıtım videosu');
            $table->string('license_type', 50)->default('standard')->comment('Lisans türü');
            $table->decimal('price', 19, 2)->default(0)->comment('Fiyat');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi');
            $table->unsignedBigInteger('tax_class_id')->default(0)->comment('Vergi sınıfı');
            $table->string('version', 20)->default('1.0.0')->comment('Versiyon');
            $table->decimal('rating', 3, 2)->default(0)->comment('Puan');
            $table->unsignedInteger('download_count')->default(0)->comment('İndirilme sayısı');
            $table->string('support_mail', 255)->nullable()->comment('Destek e-postası');
            $table->string('support_website', 255)->nullable()->comment('Destek web sitesi');
            $table->string('documentation_url', 500)->nullable()->comment('Dokümantasyon URL');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            $table->timestamp('published_at')->nullable()->comment('Yayınlanma tarihi');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['type', 'status'], 'idx_app_type_status');
            $table->index('deleted_at', 'idx_app_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_TRANSLATION, function (Blueprint $table) {
            $table->comment('Uygulama çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('application_id')->comment('Uygulama kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Uygulama adı');
            $table->string('slug', 300)->unique()->comment('SEO dostu URL');
            $table->string('summary', 500)->nullable()->comment('Özet');
            $table->longText('description')->nullable()->comment('Açıklama');
            $table->text('requirements')->nullable()->comment('Gereksinimler');
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

            $table->unique(['application_id', 'language_code'], 'idx_app_lang_unique');
            $table->fullText(['name', 'description'], 'idx_app_search_fulltext');
            $table->index('deleted_at', 'idx_app_trans_soft_delete');
        });

        Schema::create(self::TABLE_APPLICATION_IMAGE, function (Blueprint $table) {
            $table->comment('Uygulama görselleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_image_id')->comment('Görsel benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('application_id')->index()->comment('Uygulama kimliği');
            $table->string('file', 500)->comment('Dosya yolu');
            $table->string('title', 255)->nullable()->comment('Başlık/Alt etiketi');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
        });

        Schema::create(self::TABLE_APPLICATION_RELATED, function (Blueprint $table) {
            $table->comment('İlişkili/Benzer uygulamalar');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('application_related_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('application_id')->comment('Ana uygulama');
            $table->unsignedBigInteger('related_id')->comment('İlişkili uygulama');
            
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            
            $table->unique(['application_id', 'related_id'], 'idx_app_rel_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_APPLICATION_RELATED);
        Schema::dropIfExists(self::TABLE_APPLICATION_IMAGE);
        Schema::dropIfExists(self::TABLE_APPLICATION_TRANSLATION);
        Schema::dropIfExists(self::TABLE_APPLICATION);
        Schema::dropIfExists(self::TABLE_CATEGORY_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CATEGORY);
    }
};
