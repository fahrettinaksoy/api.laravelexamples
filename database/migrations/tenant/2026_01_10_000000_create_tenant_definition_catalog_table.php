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

    private const TABLE_CATEGORY = 'def_cat_category';
    private const TABLE_CATEGORY_TRANSLATION = 'def_cat_category_translation';
    private const TABLE_CATEGORY_BRAND = 'def_cat_category_brand';
    private const TABLE_BRAND = 'def_cat_brand';
    private const TABLE_BRAND_TRANSLATION = 'def_cat_brand_translation';
    private const TABLE_PRODUCT_TYPE = 'def_cat_product_type';
    private const TABLE_PRODUCT_TYPE_TRANSLATION = 'def_cat_product_type_translation';
    private const TABLE_PRODUCT_CONDITION = 'def_cat_product_condition';
    private const TABLE_PRODUCT_CONDITION_TRANSLATION = 'def_cat_product_condition_translation';

    public function up(): void
    {
        Schema::create(self::TABLE_CATEGORY, function (Blueprint $table) {
            $table->comment('Ürün kategorileri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('category_id')->comment('Kategori benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('parent_id')->default(0)->index()->comment('Üst kategori (0=Ana kategori)');
            $table->string('code', 100)->unique()->comment('Kategori benzersiz kodu');
            $table->string('image', 500)->nullable()->comment('Kategori görseli');
            $table->string('icon', 100)->nullable()->comment('İkon');
            $table->unsignedInteger('layout_id')->default(0)->comment('Özel sayfa düzeni');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerekliliği');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['parent_id', 'sort_order'], 'idx_cat_parent_sort');
            $table->index('deleted_at', 'idx_cat_soft_delete');
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

            $table->unique(['category_id', 'language_code'], 'idx_cat_lang_unique');
            $table->index('name', 'idx_cat_name_search');
            $table->index('deleted_at', 'idx_cat_trans_soft_delete');
        });

        Schema::create(self::TABLE_BRAND, function (Blueprint $table) {
            $table->comment('Marka tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('brand_id')->comment('Marka benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Marka kodu');
            $table->string('image', 500)->nullable()->comment('Görsel');
            $table->string('logo', 500)->nullable()->comment('Logo');
            $table->unsignedInteger('layout_id')->default(0)->comment('Özel sayfa düzeni');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_brand_soft_delete');
        });

        Schema::create(self::TABLE_BRAND_TRANSLATION, function (Blueprint $table) {
            $table->comment('Marka çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('brand_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('brand_id')->comment('Marka kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Marka adı');
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

            $table->unique(['brand_id', 'language_code'], 'idx_brand_lang_unique');
            $table->index('deleted_at', 'idx_brand_trans_soft_delete');
        });

        Schema::create(self::TABLE_CATEGORY_BRAND, function (Blueprint $table) {
            $table->comment('Kategori - Marka ilişkisi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('category_brand_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('category_id')->comment('Kategori kimliği');
            $table->unsignedBigInteger('brand_id')->comment('Marka kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            
            $table->unique(['category_id', 'brand_id'], 'idx_cat_brand_unique');
        });

        Schema::create(self::TABLE_PRODUCT_TYPE, function (Blueprint $table) {
            $table->comment('Ürün tipleri (Fiziksel, Hizmet, Dijital vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('type_id')->comment('Tip benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('icon', 50)->nullable()->comment('İkon');
            $table->string('color', 20)->nullable()->comment('Renk kodu');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_type_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_TYPE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün tipi çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('type_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('type_id')->comment('Tip kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Tip adı');
            $table->string('description', 255)->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['type_id', 'language_code'], 'idx_prod_type_lang_unique');
            $table->index('deleted_at', 'idx_prod_type_trans_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_CONDITION, function (Blueprint $table) {
            $table->comment('Ürün durumları (Sıfır, 2. El, Yenilenmiş vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('condition_id')->comment('Durum benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_cond_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_CONDITION_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün durumu çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('condition_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('condition_id')->comment('Durum kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Durum adı');
            $table->string('description', 255)->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['condition_id', 'language_code'], 'idx_prod_cond_lang_unique');
            $table->index('deleted_at', 'idx_prod_cond_trans_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_PRODUCT_CONDITION_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRODUCT_CONDITION);
        Schema::dropIfExists(self::TABLE_PRODUCT_TYPE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRODUCT_TYPE);
        Schema::dropIfExists(self::TABLE_CATEGORY_BRAND);
        Schema::dropIfExists(self::TABLE_BRAND_TRANSLATION);
        Schema::dropIfExists(self::TABLE_BRAND);
        Schema::dropIfExists(self::TABLE_CATEGORY_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CATEGORY);
    }
};
