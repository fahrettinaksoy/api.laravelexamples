<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_product', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlerin ana bilgilerinin tutulduğu tablo');
            
            $table->id('product_id')->comment('Ürün benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Dış entegrasyonlar için UUID');
            $table->string('code', 100)->unique()->comment('Dahili ürün kodu');
            $table->string('sku', 100)->unique()->comment('Stok takip kodu');
            $table->string('barcode', 100)->nullable()->index('idx_product_barcode')->comment('Ürün barkodu');
            $table->string('upc', 50)->nullable()->comment('Evrensel ürün kodu');
            $table->string('ean', 50)->nullable()->comment('Avrupa ürün numarası');
            $table->string('jan', 50)->nullable()->comment('Japon ürün numarası');
            $table->string('isbn', 50)->nullable()->comment('Uluslararası standart kitap numarası');
            $table->string('mpn', 100)->nullable()->comment('Üretici parça numarası');
            $table->string('oem', 100)->nullable()->comment('Orijinal ekipman üreticisi kodu');
            $table->unsignedTinyInteger('type_id')->default(1)->comment('Ürün tipi: 1-Basit, 2-Varyantlı, 3-Gruplu, 4-Paket, 5-Dijital');
            $table->unsignedInteger('condition_id')->default(1)->comment('Ürün durumu: 1-Yeni, 2-Yenilenmiş, 3-Kullanılmış');
            $table->boolean('is_adult')->default(false)->index('idx_product_is_adult')->comment('Yetişkin içerik bayrağı');
            $table->boolean('is_domestic')->default(true)->comment('Yerli ürün bayrağı');
            $table->string('origin_country', 2)->nullable()->comment('Menşei ülke kodu (ISO 3166-1)');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Kategori kimliği');
            $table->unsignedBigInteger('brand_id')->nullable()->comment('Marka kimliği');
            $table->string('model', 150)->nullable()->comment('Ürün modeli');
            $table->string('image_cover', 500)->nullable()->comment('Ana ürün görseli');
            $table->string('image_hover', 500)->nullable()->comment('Hover durumunda gösterilecek görsel');
            $table->string('video_cover', 500)->nullable()->comment('Ürün video URL\'si');
            $table->string('live_broadcast_url', 500)->nullable()->comment('Canlı yayın alışveriş URL\'si');
            $table->unsignedInteger('variant_stock_id')->default(0)->comment('Aktif varyant stok kimliği');
            $table->unsignedInteger('minimum_quantity')->default(1)->comment('Minimum sipariş adedi');
            $table->unsignedInteger('maximum_quantity')->default(0)->comment('Maksimum sipariş adedi (0=sınırsız)');
            $table->unsignedInteger('coefficient')->default(1)->comment('Adet çarpanı');
            $table->unsignedInteger('unit_id')->default(1)->comment('Ölçü birimi kimliği');
            $table->unsignedTinyInteger('stockless_id')->default(0)->comment('Stok tükendiğinde davranış');
            $table->boolean('subtract_stock')->default(true)->comment('Siparişte stoktan düş');
            $table->decimal('buy_price', 19, 4)->default(0)->comment('Alış/maliyet fiyatı');
            $table->char('buy_currency_code', 3)->default('USD')->comment('Alış para birimi (ISO 4217)');
            $table->unsignedInteger('buy_tax_class_id')->default(0)->comment('Alış vergi sınıfı kimliği');
            $table->decimal('sell_price', 19, 4)->default(0)->index('idx_product_sell_price')->comment('Satış fiyatı');
            $table->char('sell_currency_code', 3)->default('USD')->comment('Satış para birimi (ISO 4217)');
            $table->unsignedInteger('sell_tax_class_id')->default(0)->comment('Satış vergi sınıfı kimliği');
            $table->unsignedInteger('sell_point')->default(0)->comment('Satın alma için gereken puan');
            $table->decimal('discount_value', 19, 4)->default(0)->comment('İndirim değeri');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed')->comment('İndirim tipi: fixed-Sabit, percentage-Yüzde');
            $table->decimal('special_consumption_tax', 5, 2)->nullable()->comment('Özel tüketim vergisi (ÖTV)');
            $table->decimal('special_communication_tax', 5, 2)->nullable()->comment('Özel iletişim vergisi (ÖİV)');
            $table->unsignedInteger('weight_class_id')->default(1)->comment('Ağırlık birimi kimliği (kg, g, lb, oz)');
            $table->decimal('weight', 10, 3)->default(0)->comment('Ürün ağırlığı');
            $table->unsignedInteger('length_class_id')->default(1)->comment('Uzunluk birimi kimliği (cm, m, in, ft)');
            $table->decimal('length', 10, 2)->default(0)->comment('Ürün uzunluğu');
            $table->decimal('width', 10, 2)->default(0)->comment('Ürün genişliği');
            $table->decimal('height', 10, 2)->default(0)->comment('Ürün yüksekliği');
            $table->decimal('desi', 10, 2)->nullable()->comment('Hacimsel ağırlık (desi)');
            $table->unsignedInteger('warranty_period')->default(0)->comment('Garanti süresi');
            $table->enum('warranty_type', ['day', 'week', 'month', 'year'])->nullable()->comment('Garanti tipi: day-Gün, week-Hafta, month-Ay, year-Yıl');
            $table->boolean('requires_shipping')->default(true)->comment('Kargo gerektirir');
            $table->unsignedInteger('delivery_time')->default(0)->comment('Teslimat süresi');
            $table->enum('delivery_type', ['day', 'week', 'month', 'year'])->nullable()->comment('Teslimat tipi: day-Gün, week-Hafta, month-Ay, year-Yıl');
            $table->unsignedInteger('cargo_time')->default(0)->comment('Kargo süresi');
            $table->enum('cargo_type', ['day', 'week', 'month', 'year'])->nullable()->comment('Kargo tipi: day-Gün, week-Hafta, month-Ay, year-Yıl');
            $table->decimal('cargo_price', 19, 4)->nullable()->comment('Kargo ücreti');
            $table->char('cargo_currency_code', 3)->nullable()->comment('Kargo para birimi');
            $table->unsignedInteger('cargo_tax_class_id')->default(0)->comment('Kargo vergi sınıfı kimliği');
            $table->boolean('installment_enabled')->default(false)->comment('Taksit aktif');
            $table->unsignedTinyInteger('installment_rate')->default(0)->comment('Maksimum taksit sayısı');
            $table->unsignedInteger('point_reward')->default(0)->comment('Satın almada kazanılan puan');
            $table->unsignedInteger('recurring_id')->default(0)->comment('Abonelik planı kimliği');
            $table->unsignedBigInteger('viewed')->default(0)->index('idx_product_viewed')->comment('Görüntülenme sayısı');
            $table->unsignedInteger('layout_id')->default(0)->comment('Sayfa düzeni kimliği');
            $table->unsignedInteger('sort_order')->default(0)->index('idx_product_sort_order')->comment('Sıralama değeri');
            $table->boolean('is_returnable')->default(true)->comment('İade edilebilir');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektirir');
            $table->boolean('is_active')->default(true)->index('idx_product_is_active')->comment('Ürün aktif durumu');
            $table->boolean('is_published')->default(false)->index('idx_product_is_published')->comment('Yayında');
            $table->timestamp('production_date')->nullable()->comment('Üretim tarihi');
            $table->timestamp('expiration_date')->nullable()->comment('Son kullanma tarihi');
            $table->timestamp('publish_start_date')->nullable()->comment('Yayın başlangıç tarihi');
            $table->timestamp('publish_end_date')->nullable()->comment('Yayın bitiş tarihi');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes()->comment('Soft delete zamanı');
            
            $table->index(['category_id', 'is_published', 'is_active'], 'idx_product_category_published_active');
            $table->index(['brand_id', 'is_published'], 'idx_product_brand_published');
            $table->index(['sell_price', 'is_published'], 'idx_product_price_published');
            $table->index(['created_at', 'is_published'], 'idx_product_created_published');
            $table->index('uuid', 'idx_product_uuid');
        });

        Schema::create('cat_product_account_price', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Müşteri gruplarına özel ürün fiyatlandırma tablosu');
            
            $table->id('product_account_price_id')->comment('Fiyatlandırma benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('account_type_id')->comment('Müşteri grubu/seviye kimliği');
            $table->decimal('price', 19, 4)->comment('Fiyat');
            $table->char('currency_code', 3)->default('USD')->comment('Para birimi kodu (ISO 4217)');
            $table->unsignedInteger('tax_class_id')->default(0)->comment('Vergi sınıfı kimliği');
            $table->unsignedInteger('point_sales')->default(0)->comment('Satış için gereken puan');
            $table->unsignedInteger('point_reward')->default(0)->comment('Kazanılan puan');
            $table->decimal('discount_value', 19, 4)->default(0)->comment('İndirim değeri');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed')->comment('İndirim tipi: fixed-Sabit, percentage-Yüzde');
            $table->boolean('login_required')->default(false)->comment('Fiyatı sadece giriş yapan kullanıcılara göster');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'account_type_id'], 'idx_product_account_price_unique');
            $table->index(['product_id', 'account_type_id'], 'idx_product_account_price_lookup');
        });

        Schema::create('cat_product_translation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün çoklu dil çevirileri tablosu');
            
            $table->id('product_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->char('language_code', 5)->comment('Dil kodu (ISO 639-1)');
            $table->string('name', 500)->index('idx_product_translation_name')->comment('Ürün adı');
            $table->string('slug', 600)->unique()->comment('SEO dostu URL');
            $table->text('summary')->nullable()->comment('Kısa özet');
            $table->longText('description')->nullable()->comment('Detaylı açıklama');
            $table->string('tag', 1000)->nullable()->comment('Virgülle ayrılmış etiketler');
            $table->string('keyword', 1000)->nullable()->comment('Arama anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO meta başlık');
            $table->text('meta_description')->nullable()->comment('SEO meta açıklama');
            $table->string('meta_keyword', 500)->nullable()->comment('SEO meta anahtar kelimeler');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'language_code'], 'idx_product_translation_unique');
            $table->index(['language_code', 'name'], 'idx_product_translation_lang_name');
            $table->fullText(['name', 'description', 'tag', 'keyword'], 'idx_product_translation_fulltext');
        });

        Schema::create('cat_product_activity', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_activity_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('entry_id')->default(0)->comment('Transaction entry ID');
            $table->unsignedBigInteger('relation_id')->default(0)->comment('Related entity ID');
            $table->unsignedInteger('module_id')->default(0)->comment('Module/source identifier');
            $table->unsignedBigInteger('account_id')->default(0)->comment('Customer/user ID');
            $table->unsignedBigInteger('product_variant_stock_id')->default(0);
            $table->integer('quantity')->default(0)->comment('Quantity change (+/-)');
            $table->decimal('price', 19, 4)->default(0);
            $table->char('currency_code', 3)->default('USD');
            $table->unsignedInteger('tax_class_id')->default(0);
            $table->string('code', 100)->nullable()->index();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('warehouse_id')->default(0);
            $table->unsignedInteger('area_id')->default(0);
            $table->unsignedInteger('shelf_id')->default(0);
            $table->boolean('is_approved')->default(false)->index();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['product_id', 'created_at']);
            $table->index(['warehouse_id', 'product_id']);
            $table->index(['account_id', 'product_id']);
        });

        Schema::create('cat_product_reward', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_reward_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('account_group_id')->comment('Customer group ID');
            $table->unsignedInteger('point')->default(0);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'account_group_id'], 'idx_product_reward_unique');
        });

        Schema::create('cat_product_image', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_image_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            
            $table->string('file', 500);
            $table->string('alt_text', 255)->nullable()->comment('Image alt text for SEO');
            $table->string('title', 255)->nullable()->comment('Image title');
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('cat_product_video', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_video_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            
            $table->enum('source', ['youtube', 'vimeo', 'url', 'file', 'embed'])->default('youtube');
            $table->string('content', 1000)->comment('Video URL or embed code');
            $table->string('thumbnail', 500)->nullable();
            $table->string('name', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('cat_product_related', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_related_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('related_id')->comment('Related product ID');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->foreign('related_id')->references('product_id')->on('cat_product')->cascadeOnDelete();
            $table->unique(['product_id', 'related_id'], 'idx_product_related_unique');
            $table->index('related_id');
        });

        Schema::create('cat_product_post', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_post_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('post_id')->comment('Blog/article post ID');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'post_id'], 'idx_product_post_unique');
            $table->index('post_id');
        });

        Schema::create('cat_product_accessory', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_accessory_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('accessory_id')->comment('Accessory product ID');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->foreign('accessory_id')->references('product_id')->on('cat_product')->cascadeOnDelete();
            $table->unique(['product_id', 'accessory_id'], 'idx_product_accessory_unique');
        });

        Schema::create('cat_product_download', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_download_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('download_id')->comment('İlişkili tablo kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'download_id'], 'idx_product_download_unique');
        });

        Schema::create('cat_product_faq', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_faq_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('faq_id')->comment('FAQ entry ID');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'faq_id'], 'idx_product_faq_unique');
            $table->index('faq_id');
        });

        Schema::create('cat_product_attribute', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_attribute_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('attribute_variable_id')->comment('Attribute value ID');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'attribute_variable_id'], 'idx_product_attribute_unique');
            $table->index('attribute_variable_id');
        });

        Schema::create('cat_product_attribute_translation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_attribute_translation_id');
            $table->unsignedBigInteger('product_attribute_id')->comment('İlişkili tablo kimliği');
            $table->char('language_code', 5);
            $table->text('text');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_attribute_id', 'language_code'], 'idx_product_attr_lang_unique');
        });

        Schema::create('cat_product_field', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_field_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('field_type_id')->comment('Custom field type');
            $table->string('image', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['product_id', 'field_type_id']);
        });

        Schema::create('cat_product_field_translation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_field_translation_id');
            $table->unsignedBigInteger('product_field_id')->comment('İlişkili tablo kimliği');
            $table->char('language_code', 5);
            $table->string('name', 255);
            $table->text('content')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_field_id', 'language_code'], 'idx_product_field_lang_unique');
        });

        Schema::create('cat_product_filter_value', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_filter_value_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('filter_value_id');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'filter_value_id'], 'idx_product_filter_unique');
            $table->index('filter_value_id');
        });

        Schema::create('cat_product_option', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_option_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('option_id');
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('value', 500)->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'option_id'], 'idx_product_option_unique');
            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('cat_product_option_value', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_option_value_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('product_option_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('option_value_id');
            $table->string('sku', 100)->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('buy_price', 19, 4)->default(0);
            $table->enum('buy_price_prefix', ['+', '-', '='])->default('+');
            $table->decimal('sell_price', 19, 4)->default(0);
            $table->enum('sell_price_prefix', ['+', '-', '='])->default('+');
            $table->integer('sell_point')->default(0);
            $table->enum('sell_point_prefix', ['+', '-', '='])->default('+');
            $table->decimal('weight', 10, 3)->default(0);
            $table->enum('weight_prefix', ['+', '-', '='])->default('+');
            $table->decimal('discount_value', 19, 4)->default(0);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['product_id', 'product_option_id']);
            $table->index(['product_option_id', 'option_value_id']);
        });
        
        Schema::create('cat_product_variant', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_variant_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('variant_id')->comment('Variant type ID (e.g., Color, Size)');
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'variant_id'], 'idx_product_variant_unique');
            $table->index('variant_id');
        });

        Schema::create('cat_product_variant_stock', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_variant_stock_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->string('sku', 100)->unique();
            $table->integer('quantity')->default(0)->index();
            $table->decimal('buy_price', 19, 4)->default(0);
            $table->decimal('sell_price', 19, 4)->default(0);
            $table->integer('sell_point')->default(0);
            $table->decimal('weight', 10, 3)->default(0);
            $table->decimal('discount_value', 19, 4)->default(0);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['product_id', 'quantity']);
        });

        Schema::create('cat_product_variant_variable', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_variant_variable_id');
            $table->unsignedBigInteger('product_variant_stock_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('variant_id');
            $table->unsignedInteger('variant_variable_id')->comment('Specific variant value (e.g., Red, Large)');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_variant_stock_id', 'variant_id'], 'idx_variant_stock_unique');
            $table->index(['product_id', 'variant_id', 'variant_variable_id']);
        });

        Schema::create('cat_product_grouped', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_grouped_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('grouped_id')->comment('Child product ID');
            $table->unsignedInteger('opening')->default(0)->comment('Default quantity');
            $table->unsignedInteger('minimum')->default(1);
            $table->unsignedInteger('maximum')->default(0);
            $table->unsignedInteger('coefficient')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->foreign('grouped_id')->references('product_id')->on('cat_product')->cascadeOnDelete();
            $table->unique(['product_id', 'grouped_id'], 'idx_product_grouped_unique');
        });

        Schema::create('cat_product_bundle', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_bundle_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedBigInteger('bundle_id')->comment('Bundled product ID');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->foreign('bundle_id')->references('product_id')->on('cat_product')->cascadeOnDelete();
            $table->unique(['product_id', 'bundle_id'], 'idx_product_bundle_unique');
        });

        Schema::create('cat_product_recurring', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_recurring_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->unsignedInteger('recurring_type_id')->comment('Subscription plan ID');
            $table->unsignedInteger('account_type_id')->comment('Customer group');
            $table->boolean('is_default')->default(false);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_id', 'recurring_type_id', 'account_type_id'], 'idx_product_recurring_unique');
        });

        Schema::create('cat_product_document', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_document_id');
            $table->unsignedBigInteger('product_id')->comment('İlişkili tablo kimliği');
            $table->string('code', 100)->unique();
            $table->string('image', 500)->nullable()->comment('Document thumbnail');
            $table->string('file', 500)->comment('File path');
            $table->string('filename', 255)->comment('Original filename');
            $table->string('mask', 255)->nullable()->comment('Display filename');
            $table->unsignedInteger('file_size')->default(0)->comment('File size in bytes');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->index(['product_id', 'is_active']);
        });

        Schema::create('cat_product_document_translation', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->id('product_document_translation_id');
            $table->unsignedBigInteger('product_document_id')->comment('İlişkili tablo kimliği');
            $table->char('language_code', 5);
            $table->string('name', 255);
            $table->string('summary', 500)->nullable();
            $table->text('description')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->softDeletes();
            
            $table->unique(['product_document_id', 'language_code'], 'idx_product_doc_lang_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_product_document_translation');
        Schema::dropIfExists('cat_product_document');
        Schema::dropIfExists('cat_product_recurring');
        Schema::dropIfExists('cat_product_bundle');
        Schema::dropIfExists('cat_product_grouped');
        Schema::dropIfExists('cat_product_variant_variable');
        Schema::dropIfExists('cat_product_variant_stock');
        Schema::dropIfExists('cat_product_variant');
        Schema::dropIfExists('cat_product_option_value');
        Schema::dropIfExists('cat_product_option');
        Schema::dropIfExists('cat_product_filter_value');
        Schema::dropIfExists('cat_product_field_translation');
        Schema::dropIfExists('cat_product_field');
        Schema::dropIfExists('cat_product_attribute_translation');
        Schema::dropIfExists('cat_product_attribute');
        Schema::dropIfExists('cat_product_faq');
        Schema::dropIfExists('cat_product_download');
        Schema::dropIfExists('cat_product_accessory');
        Schema::dropIfExists('cat_product_post');
        Schema::dropIfExists('cat_product_related');
        Schema::dropIfExists('cat_product_video');
        Schema::dropIfExists('cat_product_image');
        Schema::dropIfExists('cat_product_reward');
        Schema::dropIfExists('cat_product_activity');
        Schema::dropIfExists('cat_product_translation');
        Schema::dropIfExists('cat_product_account_price');
        Schema::dropIfExists('cat_product');
    }
};
