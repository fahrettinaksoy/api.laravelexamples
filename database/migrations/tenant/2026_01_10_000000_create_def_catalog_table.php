<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_CATEGORY = 'def_cat_category';
    private const TABLE_TRANSLATION = 'def_cat_category_translation';
    private const TABLE_BRAND = 'def_cat_category_brand';
    private const TABLE_FILTER = 'def_cat_category_filter';

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

        Schema::create('def_cat_brand', function (Blueprint $table) {
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
        
        Schema::create('def_cat_brand_translation', function (Blueprint $table) {
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

        Schema::create('def_cat_product_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_id');
			$table->string('icon', 255);
			$table->string('color', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_condition', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('condition_id');
			$table->string('sort_order', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_condition_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('condition_translation_id');
			$table->integer('condition_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_stockless', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('stockless_id');
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_stockless_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('stockless_translation_id');
			$table->integer('stockless_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_variant', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('variant_id');
			$table->string('code', 255);
			$table->string('type', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_variant_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('variant_translation_id');
			$table->integer('variant_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_variant_variable', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('variant_variable_id');
			$table->integer('variant_id')->default(0);
			$table->string('image', 255);
			$table->string('color', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_variant_variable_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('variant_variable_translation_id');
			$table->integer('variant_variable_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_option_variable', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('option_variable_id');
			$table->string('code', 255);
			$table->string('type', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_option_variable_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('option_variable_translation_id');
			$table->integer('option_variable_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_option_value', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('option_value_id');
			$table->integer('option_id')->default(0);
			$table->string('code', 255);
			$table->string('image', 255);
			$table->string('color', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_option_value_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('option_value_translation_id');
			$table->integer('option_value_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_field_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('field_type_id');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_field_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('field_type_translation_id');
			$table->integer('field_type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_relation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('relation_id');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_relation_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('relation_translation_id');
			$table->integer('relation_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
	
        Schema::create('def_cat_product_filter_variable', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('filter_variable_id');
			$table->string('code', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_filter_variable_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('filter_variable_translation_id');
			$table->index('filter_variable_id');
			$table->integer('filter_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_filter_value', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('filter_value_id');
			$table->index('filter_id');
			$table->integer('filter_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_filter_value_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('filter_value_translation_id');
			$table->index('filter_value_id');
			$table->integer('filter_value_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_attribute_template', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_template_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_attribute_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_group_id');
			$table->string('code', 255);
			$table->integer('attribute_template_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_attribute_group_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_group_translation_id');
			$table->integer('attribute_group_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_attribute_variable', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_variable_id');
			$table->string('code', 255);
			$table->integer('attribute_group_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_attribute_variable_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('attribute_variable_translation_id');
			$table->integer('attribute_variable_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_recurring_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('recurring_type_id');
			$table->string('code', 255);
			$table->integer('duration')->default(0);
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly']);
			$table->integer('cycle')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->decimal('trial_price', 19, 2);
			$table->integer('trial_duration')->default(0);
            $table->enum('trial_frequency', ['daily', 'weekly', 'monthly', 'yearly']);
			$table->integer('trial_cycle')->default(0);
			$table->tinyInteger('trial_status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cat_product_recurring_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('recurring_type_translation_id');
			$table->integer('recurring_type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('def_cat_stock_entry');
        Schema::dropIfExists('def_cat_stock_entry_translation');
        
        Schema::dropIfExists('def_cat_product_condition');
        Schema::dropIfExists('def_cat_product_condition_translation');
        Schema::dropIfExists('def_cat_product_stockless');
        Schema::dropIfExists('def_cat_product_stockless_translation');
        Schema::dropIfExists('def_cat_product_variant');
        Schema::dropIfExists('def_cat_product_variant_translation');
        Schema::dropIfExists('def_cat_product_variant_variable');
        Schema::dropIfExists('def_cat_product_variant_variable_translation');
        Schema::dropIfExists('def_cat_product_option');
        Schema::dropIfExists('def_cat_product_option_translation');
        Schema::dropIfExists('def_cat_product_option_value');
        Schema::dropIfExists('def_cat_product_option_value_translation');
        Schema::dropIfExists('def_cat_product_filter');
        Schema::dropIfExists('def_cat_product_filter_translation');
        Schema::dropIfExists('def_cat_product_filter_value');
        Schema::dropIfExists('def_cat_product_filter_value_translation');
        Schema::dropIfExists('def_cat_product_attribute_template');
        Schema::dropIfExists('def_cat_product_attribute_group');
        Schema::dropIfExists('def_cat_product_attribute_group_translation');
        Schema::dropIfExists('def_cat_product_attribute_variable');
        Schema::dropIfExists('def_cat_product_attribute_variable_translation');
        Schema::dropIfExists('def_cat_product_field_type');
        Schema::dropIfExists('def_cat_product_field_type_translation');
        Schema::dropIfExists('def_cat_product_relation');
        Schema::dropIfExists('def_cat_product_relation_translation');
        Schema::dropIfExists('def_cat_product_recurring_type');
        Schema::dropIfExists('def_cat_product_recurring_type_translation');
        
        Schema::dropIfExists('def_cat_brand_translation');
        Schema::dropIfExists('def_cat_brand');
        
        Schema::dropIfExists(self::TABLE_FILTER);
        Schema::dropIfExists(self::TABLE_BRAND);
        Schema::dropIfExists(self::TABLE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_CATEGORY);
    }
};
