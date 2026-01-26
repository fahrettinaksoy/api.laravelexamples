<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_CATEGORY = 'cat_category';
    private const TABLE_TRANSLATION = 'cat_category_translation';
    private const TABLE_BRAND = 'cat_category_brand';
    private const TABLE_FILTER = 'cat_category_filter';

    public function up(): void
    {
        Schema::create(self::TABLE_CATEGORY, function (Blueprint $table) {
            $table->comment('Kategori ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('category_id')->comment('Kategori benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('parent_id')->default(0)->index('idx_auto')->comment('Üst kategori kimliği (0 = ana kategori)');
            $table->string('code', 100)->unique()->comment('Kategori benzersiz kodu');
            $table->string('image', 500)->nullable()->comment('Kategori görseli URL');
            $table->string('icon', 100)->nullable()->comment('Kategori ikonu (CSS class veya SVG)');
            $table->unsignedInteger('layout_id')->default(0)->comment('Kategori sayfa düzeni kimliği');
            $table->unsignedInteger('sort_order')->default(0)->comment('Kategori sıralama numarası');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektiriyor mu?');
            $table->boolean('is_active')->default(true)->index()->comment('Kategori aktif mi?');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['parent_id', 'is_active', 'sort_order', 'deleted_at'], 'idx_category_hierarchy');
            $table->index(['is_active', 'deleted_at'], 'idx_category_active_status');
            $table->index(['code', 'deleted_at'], 'idx_category_code_lookup');
            $table->index(['layout_id', 'is_active'], 'idx_category_layout');
            $table->index(['requires_membership', 'is_active'], 'idx_category_membership');
            $table->index(['created_by', 'created_at'], 'idx_category_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_category_updated_audit');
            $table->index('deleted_at', 'idx_category_soft_delete');
        });

        Schema::create(self::TABLE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Kategori çoklu dil çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('category_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('category_id')->comment('İlişkili kategori kimliği');
            $table->char('language_code', 5)->comment('Dil kodu (tr, en, vb.)');
            $table->string('name', 255)->index()->comment('Kategori adı');
            $table->string('slug', 300)->unique()->comment('SEO dostu URL');
            $table->text('summary')->nullable()->comment('Kategori kısa özeti');
            $table->longText('description')->nullable()->comment('Kategori detaylı açıklaması');
            $table->string('keyword', 1000)->nullable()->comment('Arama anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO meta başlığı');
            $table->text('meta_description')->nullable()->comment('SEO meta açıklaması');
            $table->string('meta_keyword', 500)->nullable()->comment('SEO meta anahtar kelimeleri');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['category_id', 'language_code'], 'idx_category_lang_unique');
            $table->index(['category_id', 'language_code', 'deleted_at'], 'idx_translation_lookup');
            $table->index(['language_code', 'deleted_at'], 'idx_translation_lang');
            $table->index(['slug', 'language_code', 'deleted_at'], 'idx_translation_slug_lookup');
            $table->index(['name', 'language_code'], 'idx_translation_name_search');
            $table->index(['created_by', 'created_at'], 'idx_translation_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_translation_updated_audit');
            $table->index('deleted_at', 'idx_translation_soft_delete');
            $table->fullText(['name', 'description', 'keyword'], 'idx_translation_fulltext');
        });

        Schema::create(self::TABLE_BRAND, function (Blueprint $table) {
            $table->comment('Kategori-Marka ilişki tablosu (pivot)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('category_brand_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('category_id')->comment('İlişkili kategori kimliği');
            $table->unsignedBigInteger('brand_id')->comment('İlişkili marka kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['category_id', 'brand_id'], 'idx_category_brand_unique');
            $table->index(['category_id', 'deleted_at'], 'idx_brand_by_category');
            $table->index(['brand_id', 'deleted_at'], 'idx_category_by_brand');
            $table->index(['brand_id', 'category_id', 'deleted_at'], 'idx_brand_category_lookup');
            $table->index(['created_by', 'created_at'], 'idx_brand_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_brand_updated_audit');
            $table->index('deleted_at', 'idx_brand_soft_delete');
        });

        Schema::create(self::TABLE_FILTER, function (Blueprint $table) {
            $table->comment('Kategori-Filtre değeri ilişki tablosu (pivot)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('category_filter_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('category_id')->comment('İlişkili kategori kimliği');
            $table->unsignedInteger('filter_value_id')->comment('İlişkili filtre değeri kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
        
            $table->unique(['category_id', 'filter_value_id'], 'idx_category_filter_unique');
            $table->index(['category_id', 'deleted_at'], 'idx_filter_by_category');
            $table->index(['filter_value_id', 'deleted_at'], 'idx_category_by_filter');
            $table->index(['filter_value_id', 'category_id', 'deleted_at'], 'idx_filter_category_lookup');
            $table->index(['created_by', 'created_at'], 'idx_filter_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_filter_updated_audit');
            $table->index('deleted_at', 'idx_filter_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_FILTER);
        Schema::dropIfExists(self::TABLE_BRAND);
        Schema::dropIfExists(self::TABLE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CATEGORY);
    }
};
