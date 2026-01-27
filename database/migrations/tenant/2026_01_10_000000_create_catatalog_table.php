<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_PRODUCT_PRODUCT = 'cat_product';
    private const TABLE_PRODUCT_ACCOUNT_PRICE = 'cat_product_account_price';
    private const TABLE_PRODUCT_TRANSLATION = 'cat_product_translation';
    private const TABLE_PRODUCT_ACTIVITY = 'cat_product_activity';
    private const TABLE_PRODUCT_REWARD = 'cat_product_reward';
    private const TABLE_PRODUCT_IMAGE = 'cat_product_image';
    private const TABLE_PRODUCT_VIDEO = 'cat_product_video';
    private const TABLE_PRODUCT_RELATED = 'cat_product_related';
    private const TABLE_PRODUCT_POST = 'cat_product_post';
    private const TABLE_PRODUCT_ACCESSORY = 'cat_product_accessory';
    private const TABLE_PRODUCT_DOWNLOAD = 'cat_product_download';
    private const TABLE_PRODUCT_FAQ = 'cat_product_faq';
    private const TABLE_PRODUCT_ATTRIBUTE = 'cat_product_attribute';
    private const TABLE_PRODUCT_ATTRIBUTE_TRANSLATION = 'cat_product_attribute_translation';
    private const TABLE_PRODUCT_FIELD = 'cat_product_field';
    private const TABLE_PRODUCT_FIELD_TRANSLATION = 'cat_product_field_translation';
    private const TABLE_PRODUCT_FILTER_VALUE = 'cat_product_filter_value';
    private const TABLE_PRODUCT_OPTION = 'cat_product_option';
    private const TABLE_PRODUCT_OPTION_VALUE = 'cat_product_option_value';
    private const TABLE_PRODUCT_VARIANT = 'cat_product_variant';
    private const TABLE_PRODUCT_VARIANT_STOCK = 'cat_product_variant_stock';
    private const TABLE_PRODUCT_VARIANT_VARIABLE = 'cat_product_variant_variable';
    private const TABLE_PRODUCT_GROUPED = 'cat_product_grouped';
    private const TABLE_PRODUCT_BUNDLE = 'cat_product_bundle';
    private const TABLE_PRODUCT_RECURRING = 'cat_product_recurring';
    private const TABLE_PRODUCT_DOCUMENT = 'cat_product_document';
    private const TABLE_PRODUCT_DOCUMENT_TRANSLATION = 'cat_product_document_translation';
    
    private const TABLE_DOWNLOAD = 'cat_download';
    private const TABLE_DOWNLOAD_TRANSLATION = 'cat_download_translation';

    private const TABLE_REVIEW = 'cat_review';

    private const TABLE_WAREHOUSE = 'cat_warehouse';

    public function up(): void
    {
        Schema::create(self::TABLE_PRODUCT_PRODUCT, function (Blueprint $table) {
            $table->comment('Ürünlerin ana bilgilerinin tutulduğu tablo');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_id')->comment('Ürün benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Dış entegrasyonlar için UUID');
            $table->string('code', 100)->unique()->comment('Dahili ürün kodu');
            $table->string('sku', 100)->unique()->comment('Stok takip kodu');
            $table->string('barcode', 100)->nullable()->index()->comment('Ürün barkodu');
            $table->string('upc', 50)->nullable()->comment('Evrensel ürün kodu');
            $table->string('ean', 50)->nullable()->comment('Avrupa ürün numarası');
            $table->string('jan', 50)->nullable()->comment('Japon ürün numarası');
            $table->string('isbn', 50)->nullable()->comment('Uluslararası standart kitap numarası');
            $table->string('mpn', 100)->nullable()->comment('Üretici parça numarası');
            $table->string('oem', 100)->nullable()->comment('Orijinal ekipman üreticisi kodu');
            $table->unsignedTinyInteger('type_id')->default(1)->comment('Ürün tipi: 1-Basit, 2-Varyantlı, 3-Gruplu, 4-Paket, 5-Dijital');
            $table->unsignedInteger('condition_id')->default(1)->comment('Ürün durumu: 1-Yeni, 2-Yenilenmiş, 3-Kullanılmış');
            $table->boolean('is_adult')->default(false)->comment('Yetişkin içerik bayrağı');
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
            $table->decimal('sell_price', 19, 4)->default(0)->comment('Satış fiyatı');
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
            $table->unsignedBigInteger('viewed')->default(0)->comment('Görüntülenme sayısı');
            $table->unsignedInteger('layout_id')->default(0)->comment('Sayfa düzeni kimliği');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama değeri');
            $table->boolean('is_returnable')->default(true)->comment('İade edilebilir');
            $table->boolean('requires_membership')->default(false)->comment('Üyelik gerektirir');
            $table->boolean('is_active')->default(true)->index()->comment('Ürün aktif durumu');
            $table->boolean('is_published')->default(false)->index()->comment('Yayında');
            $table->timestamp('production_date')->nullable()->comment('Üretim tarihi');
            $table->timestamp('expiration_date')->nullable()->comment('Son kullanma tarihi');
            $table->timestamp('publish_start_date')->nullable()->comment('Yayın başlangıç tarihi');
            $table->timestamp('publish_end_date')->nullable()->comment('Yayın bitiş tarihi');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['category_id', 'is_published', 'is_active'], 'idx_product_cat_pub_active');
            $table->index(['brand_id', 'is_published'], 'idx_product_brand_pub');
            $table->index(['sell_price', 'is_published'], 'idx_product_price_pub');
            $table->index(['created_at', 'is_published'], 'idx_product_created_pub');
            $table->index(['is_adult', 'is_active'], 'idx_product_adult_active');
            $table->index(['viewed', 'is_active'], 'idx_product_viewed_active');
            $table->index(['sort_order', 'is_active'], 'idx_product_sort_active');
            $table->index(['created_by', 'created_at'], 'idx_product_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_product_updated_audit');
            $table->index('deleted_at', 'idx_product_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ACCOUNT_PRICE, function (Blueprint $table) {
            $table->comment('Müşteri gruplarına özel ürün fiyatlandırma tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_account_price_id')->comment('Fiyatlandırma benzersiz kimliği');
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
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'account_type_id'], 'idx_prod_acc_price_unique');
            $table->index(['product_id', 'deleted_at'], 'idx_acc_price_product_lookup');
            $table->index('deleted_at', 'idx_prod_acc_price_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün çoklu dil çevirileri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->char('language_code', 5)->comment('Dil kodu (ISO 639-1)');
            $table->string('name', 500)->index()->comment('Ürün adı');
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
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'language_code'], 'idx_prod_trans_unique');
            $table->index(['language_code', 'name'], 'idx_prod_trans_lang_name');
            $table->index('deleted_at', 'idx_prod_trans_soft_delete');
            $table->fullText(['name', 'description', 'tag', 'keyword'], 'idx_prod_trans_fulltext');
        });

        Schema::create(self::TABLE_PRODUCT_ACTIVITY, function (Blueprint $table) {
            $table->comment('Ürün aktivite/hareket geçmişi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_activity_id')->comment('Aktivite benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedBigInteger('entry_id')->default(0)->comment('İşlem giriş kimliği');
            $table->unsignedBigInteger('relation_id')->default(0)->comment('İlişkili kayıt kimliği');
            $table->unsignedInteger('module_id')->default(0)->comment('Modül/Kaynak tanımlayıcı');
            $table->unsignedBigInteger('account_id')->default(0)->comment('Müşteri/Kullanıcı kimliği');
            $table->unsignedBigInteger('product_variant_stock_id')->default(0)->comment('Varyant stok kimliği');
            $table->integer('quantity')->default(0)->comment('Miktar değişimi (+/-)');
            $table->decimal('price', 19, 4)->default(0)->comment('İşlem fiyatı');
            $table->char('currency_code', 3)->default('USD')->comment('Para birimi');
            $table->unsignedInteger('tax_class_id')->default(0)->comment('Vergi sınıfı');
            $table->string('code', 100)->nullable()->index()->comment('İşlem kodu');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->unsignedBigInteger('warehouse_id')->default(0)->comment('Depo kimliği');
            $table->unsignedInteger('area_id')->default(0)->comment('Alan kimliği');
            $table->unsignedInteger('shelf_id')->default(0)->comment('Raf kimliği');
            $table->boolean('is_approved')->default(false)->index()->comment('Onay durumu');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['product_id', 'created_at'], 'idx_prod_activity_created');
            $table->index(['warehouse_id', 'product_id'], 'idx_prod_activity_warehouse');
            $table->index(['account_id', 'product_id'], 'idx_prod_activity_account');
            $table->index('deleted_at', 'idx_prod_activity_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_REWARD, function (Blueprint $table) {
            $table->comment('Ürün ödül puanları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_reward_id')->comment('Ödül benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('account_group_id')->comment('Müşteri grubu kimliği');
            $table->unsignedInteger('point')->default(0)->comment('Kazanılacak puan');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'account_group_id'], 'idx_product_reward_unique');
            $table->index('deleted_at', 'idx_prod_reward_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_IMAGE, function (Blueprint $table) {
            $table->comment('Ürün görselleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_image_id')->comment('Görsel benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            
            $table->string('file', 500)->comment('Dosya yolu/URL');
            $table->string('alt_text', 255)->nullable()->comment('SEO alt metni');
            $table->string('title', 255)->nullable()->comment('Görsel başlığı');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['product_id', 'sort_order'], 'idx_prod_image_sort');
            $table->index('deleted_at', 'idx_prod_image_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_VIDEO, function (Blueprint $table) {
            $table->comment('Ürün videoları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_video_id')->comment('Video benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            
            $table->enum('source', ['youtube', 'vimeo', 'url', 'file', 'embed'])->default('youtube')->comment('Video kaynağı');
            $table->string('content', 1000)->comment('Video URL veya embed kodu');
            $table->string('thumbnail', 500)->nullable()->comment('Önizleme görseli');
            $table->string('name', 255)->nullable()->comment('Video adı');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['product_id', 'sort_order'], 'idx_prod_video_sort');
            $table->index('deleted_at', 'idx_prod_video_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_RELATED, function (Blueprint $table) {
            $table->comment('Benzer ürünler tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_related_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ana ürün kimliği');
            $table->unsignedBigInteger('related_id')->comment('Benzer ürün kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'related_id'], 'idx_product_related_unique');
            $table->index('related_id', 'idx_prod_related_lookup');
            $table->index('deleted_at', 'idx_prod_related_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_POST, function (Blueprint $table) {
            $table->comment('Ürün-Blog yazısı ilişki tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_post_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedBigInteger('post_id')->comment('Blog/Yazı kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'post_id'], 'idx_product_post_unique');
            $table->index('post_id', 'idx_prod_post_lookup');
            $table->index('deleted_at', 'idx_prod_post_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ACCESSORY, function (Blueprint $table) {
            $table->comment('Aksesuar ürünler tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_accessory_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ana ürün kimliği');
            $table->unsignedBigInteger('accessory_id')->comment('Aksesuar ürün kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'accessory_id'], 'idx_product_accessory_unique');
            $table->index('deleted_at', 'idx_prod_accessory_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_DOWNLOAD, function (Blueprint $table) {
            $table->comment('Ürün-İndirilebilir içerik ilişki tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_download_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedBigInteger('download_id')->comment('İndirilebilir içerik kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'download_id'], 'idx_product_download_unique');
            $table->index('deleted_at', 'idx_prod_download_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_FAQ, function (Blueprint $table) {
            $table->comment('Ürün-SSS ilişki tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_faq_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedBigInteger('faq_id')->comment('SSS kayıt kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'faq_id'], 'idx_product_faq_unique');
            $table->index('faq_id', 'idx_prod_faq_lookup');
            $table->index('deleted_at', 'idx_prod_faq_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ATTRIBUTE, function (Blueprint $table) {
            $table->comment('Ürün özellik değerleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_attribute_id')->comment('Özellik değeri benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('attribute_variable_id')->comment('Özellik değişken kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'attribute_variable_id'], 'idx_product_attribute_unique');
            $table->index('attribute_variable_id', 'idx_prod_attr_lookup');
            $table->index('deleted_at', 'idx_prod_attr_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ATTRIBUTE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün özellik değeri çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_attribute_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('product_attribute_id')->comment('Ürün özellik kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->text('text')->comment('Özellik değeri (Metin)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_attribute_id', 'language_code'], 'idx_product_attr_lang_unique');
            $table->index('deleted_at', 'idx_prod_attr_trans_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_FIELD, function (Blueprint $table) {
            $table->comment('Ürün özel alanlar tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_field_id')->comment('Özel alan benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('field_type_id')->comment('Alan tipi kimliği');
            $table->string('image', 500)->nullable()->comment('Görsel');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['product_id', 'field_type_id'], 'idx_prod_field_type');
            $table->index('deleted_at', 'idx_prod_field_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_FIELD_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün özel alan çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_field_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('product_field_id')->comment('Özel alan kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Alan adı');
            $table->text('content')->nullable()->comment('İçerik');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_field_id', 'language_code'], 'idx_product_field_lang_unique');
            $table->index('deleted_at', 'idx_prod_field_trans_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_FILTER_VALUE, function (Blueprint $table) {
            $table->comment('Ürün-Filtre değeri ilişki tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_filter_value_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('filter_value_id')->comment('Filtre değeri kimliği');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'filter_value_id'], 'idx_product_filter_unique');
            $table->index('filter_value_id', 'idx_prod_filter_lookup');
            $table->index('deleted_at', 'idx_prod_filter_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_OPTION, function (Blueprint $table) {
            $table->comment('Ürün seçenekleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_option_id')->comment('Seçenek benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('option_id')->comment('Seçenek tanım kimliği');
            $table->boolean('is_required')->default(false)->comment('Zorunlu alan mı?');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->string('value', 500)->nullable()->comment('Varsayılan değer');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'option_id'], 'idx_product_option_unique');
            $table->index(['product_id', 'sort_order'], 'idx_prod_option_sort');
            $table->index('deleted_at', 'idx_prod_option_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_OPTION_VALUE, function (Blueprint $table) {
            $table->comment('Ürün seçenek değerleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_option_value_id')->comment('Değer benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedBigInteger('product_option_id')->comment('Ürün seçenek kimliği');
            $table->unsignedInteger('option_value_id')->comment('Seçenek değer tanım kimliği');
            $table->string('sku', 100)->nullable()->comment('Varyasyon stok kodu');
            $table->integer('quantity')->default(0)->comment('Stok adedi');
            $table->decimal('buy_price', 19, 4)->default(0)->comment('Alış fiyatı farkı');
            $table->enum('buy_price_prefix', ['+', '-', '='])->default('+')->comment('İşlem türü');
            $table->decimal('sell_price', 19, 4)->default(0)->comment('Satış fiyatı farkı');
            $table->enum('sell_price_prefix', ['+', '-', '='])->default('+')->comment('İşlem türü');
            $table->integer('sell_point')->default(0)->comment('Puan farkı');
            $table->enum('sell_point_prefix', ['+', '-', '='])->default('+')->comment('İşlem türü');
            $table->decimal('weight', 10, 3)->default(0)->comment('Ağırlık farkı');
            $table->enum('weight_prefix', ['+', '-', '='])->default('+')->comment('İşlem türü');
            $table->decimal('discount_value', 19, 4)->default(0)->comment('İndirim farkı');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed')->comment('İndirim tipi');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['product_id', 'product_option_id'], 'idx_prod_opt_val_lookup');
            $table->index(['product_option_id', 'option_value_id'], 'idx_prod_opt_val_detail');
            $table->index('deleted_at', 'idx_prod_opt_val_soft_delete');
        });
        
        Schema::create(self::TABLE_PRODUCT_VARIANT, function (Blueprint $table) {
            $table->comment('Ürün varyant tanımları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_variant_id')->comment('Varyant benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('variant_id')->comment('Varyant tipi kimliği (Renk, Beden vb.)');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'variant_id'], 'idx_product_variant_unique');
            $table->index('variant_id', 'idx_prod_variant_lookup');
            $table->index('deleted_at', 'idx_prod_variant_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_VARIANT_STOCK, function (Blueprint $table) {
            $table->comment('Ürün varyant stok ve fiyatlandırma tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_variant_stock_id')->comment('Stok benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->string('sku', 100)->unique()->comment('Varyant stok kodu');
            $table->integer('quantity')->default(0)->index()->comment('Stok adedi');
            $table->decimal('buy_price', 19, 4)->default(0)->comment('Alış fiyatı');
            $table->decimal('sell_price', 19, 4)->default(0)->comment('Satış fiyatı');
            $table->integer('sell_point')->default(0)->comment('Satış puanı');
            $table->decimal('weight', 10, 3)->default(0)->comment('Ağırlık');
            $table->decimal('discount_value', 19, 4)->default(0)->comment('İndirim değeri');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed')->comment('İndirim tipi');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['product_id', 'quantity'], 'idx_prod_var_stock_lookup');
            $table->index('deleted_at', 'idx_prod_var_stock_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_VARIANT_VARIABLE, function (Blueprint $table) {
            $table->comment('Varyant-Değer eşleştirme tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_variant_variable_id')->comment('Eşleştirme benzersiz kimliği');
            $table->unsignedBigInteger('product_variant_stock_id')->comment('Varyant stok kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('variant_id')->comment('Varyant tipi kimliği');
            $table->unsignedInteger('variant_variable_id')->comment('Varyant değer kimliği (Kırmızı, Large vb.)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_variant_stock_id', 'variant_id'], 'idx_variant_stock_unique');
            $table->index(['product_id', 'variant_id', 'variant_variable_id'], 'idx_prod_var_val_lookup');
            $table->index('deleted_at', 'idx_prod_var_var_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_GROUPED, function (Blueprint $table) {
            $table->comment('Gruplu ürünler tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_grouped_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ana ürün kimliği');
            $table->unsignedBigInteger('grouped_id')->comment('Grup üyesi ürün kimliği');
            $table->unsignedInteger('opening')->default(0)->comment('Varsayılan adet');
            $table->unsignedInteger('minimum')->default(1)->comment('Minimum adet');
            $table->unsignedInteger('maximum')->default(0)->comment('Maksimum adet');
            $table->unsignedInteger('coefficient')->default(1)->comment('Çarpan');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'grouped_id'], 'idx_product_grouped_unique');
            $table->index('deleted_at', 'idx_prod_grouped_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_BUNDLE, function (Blueprint $table) {
            $table->comment('Paket ürünler tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_bundle_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ana ürün kimliği');
            $table->unsignedBigInteger('bundle_id')->comment('Paket içeriği ürün kimliği');
            $table->unsignedInteger('quantity')->default(1)->comment('Adet');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'bundle_id'], 'idx_product_bundle_unique');
            $table->index('deleted_at', 'idx_prod_bundle_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_RECURRING, function (Blueprint $table) {
            $table->comment('Abonelik/Tekrarlı ödeme ürünleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_recurring_id')->comment('İlişki benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('recurring_type_id')->comment('Abonelik tipi kimliği');
            $table->unsignedInteger('account_type_id')->comment('Müşteri grubu kimliği');
            $table->boolean('is_default')->default(false)->comment('Varsayılan plan mı?');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_id', 'recurring_type_id', 'account_type_id'], 'idx_product_recurring_unique');
            $table->index('deleted_at', 'idx_prod_recurring_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_DOCUMENT, function (Blueprint $table) {
            $table->comment('Ürün belgeleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_document_id')->comment('Belge benzersiz kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->string('code', 100)->unique()->comment('Belge kodu');
            $table->string('image', 500)->nullable()->comment('Belge görseli');
            $table->string('file', 500)->comment('Dosya yolu');
            $table->string('filename', 255)->comment('Orijinal dosya adı');
            $table->string('mask', 255)->nullable()->comment('Görünen ad');
            $table->unsignedInteger('file_size')->default(0)->comment('Dosya boyutu (byte)');
            $table->string('mime_type', 100)->nullable()->comment('Dosya türü (Mime Type)');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('is_active')->default(true)->comment('Aktif mi?');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['product_id', 'is_active'], 'idx_prod_doc_active');
            $table->index('deleted_at', 'idx_prod_doc_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_DOCUMENT_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün belge çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('product_document_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->unsignedBigInteger('product_document_id')->comment('Ürün belge kimliği');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Belge adı');
            $table->string('summary', 500)->nullable()->comment('Özet');
            $table->text('description')->nullable()->comment('Açıklama');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['product_document_id', 'language_code'], 'idx_product_doc_lang_unique');
            $table->index('deleted_at', 'idx_prod_doc_trans_soft_delete');
        });
        
        Schema::create(self::TABLE_DOWNLOAD, function (Blueprint $table) {
            $table->comment('İndirme ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('download_id');
            $table->uuid('uuid')->unique();
            $table->string('code', 100)->unique();
            $table->string('filename', 500)->comment('File path');
            $table->string('mask', 255)->nullable()->comment('Display filename');
            $table->unsignedInteger('file_size')->default(0);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_active')->default(true)->index();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['is_active', 'deleted_at'], 'idx_download_active_status');
            $table->index(['code', 'deleted_at'], 'idx_download_code_lookup');
            $table->index(['created_by', 'created_at'], 'idx_download_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_download_updated_audit');
            $table->index('deleted_at', 'idx_download_soft_delete');
        });

        Schema::create(self::TABLE_DOWNLOAD_TRANSLATION, function (Blueprint $table) {
            $table->comment('İndirme çeviri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('download_translation_id');
            $table->unsignedBigInteger('download_id')->comment('İlişkili tablo kimliği');
            $table->char('language_code', 5);
            $table->string('name', 255);
            $table->text('description')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->unique(['download_id', 'language_code'], 'idx_download_lang_unique');
            $table->index(['download_id', 'language_code', 'deleted_at'], 'idx_download_trans_lookup');
            $table->index(['language_code', 'deleted_at'], 'idx_download_trans_lang');
            $table->index(['name', 'language_code'], 'idx_download_trans_name_search');
            $table->index(['created_by', 'created_at'], 'idx_download_trans_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_download_trans_updated_audit');
            $table->index('deleted_at', 'idx_download_trans_soft_delete');
        });
        
        Schema::create(self::TABLE_REVIEW, function (Blueprint $table) {
            $table->comment('Değerlendirme/Yorum ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('review_id')->comment('Yorum benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->comment('İlişkili ürün kimliği');
            $table->unsignedBigInteger('account_id')->default(0)->index('idx_auto')->comment('Müşteri/Kullanıcı kimliği');
            $table->unsignedBigInteger('order_id')->default(0)->comment('Doğrulanmış sipariş kimliği');
            $table->string('code', 100)->unique()->comment('Yorum benzersiz kodu');
            $table->string('author', 255)->comment('Yorumu yapan kişi adı');
            $table->string('email', 255)->nullable()->comment('Yorumu yapan kişi e-posta adresi');
            $table->text('content')->comment('Yorum içeriği');
            $table->unsignedTinyInteger('rating')->default(5)->comment('Genel puan (1-5)');
            $table->unsignedTinyInteger('quality_rating')->default(0)->comment('Kalite puanı (1-5)');
            $table->unsignedTinyInteger('value_rating')->default(0)->comment('Fiyat/Performans puanı (1-5)');
            $table->unsignedTinyInteger('service_rating')->default(0)->comment('Hizmet puanı (1-5)');
            $table->unsignedInteger('helpful_count')->default(0)->comment('Yararlı bulan sayısı');
            $table->unsignedInteger('not_helpful_count')->default(0)->comment('Yararlı bulmayan sayısı');
            $table->boolean('is_verified_purchase')->default(false)->comment('Doğrulanmış satın alma mı?');
            $table->boolean('is_active')->default(false)->index()->comment('Onay durumu (Aktif mi?)');
            $table->ipAddress('ip_address')->nullable()->comment('IP adresi');
            $table->string('user_agent', 500)->nullable()->comment('Tarayıcı bilgisi');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['product_id', 'is_active', 'rating'], 'idx_review_product_status');
            $table->index(['account_id', 'product_id'], 'idx_review_account_product');
            $table->index(['is_verified_purchase', 'is_active'], 'idx_review_verified_status');
            $table->index(['rating', 'is_active'], 'idx_review_rating');
            $table->index(['code', 'deleted_at'], 'idx_review_code_lookup');
            $table->index(['created_by', 'created_at'], 'idx_review_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_review_updated_audit');
            $table->index('deleted_at', 'idx_review_soft_delete');
        });

        Schema::create(self::TABLE_WAREHOUSE, function (Blueprint $table) {
            $table->comment('Depo ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;
            
            $table->bigIncrements('warehouse_id')->comment('Depo benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('category_id')->default(0)->index()->comment('Depo türü/kategorisi');
            $table->string('code', 100)->unique()->comment('Depo benzersiz kodu');
            $table->string('name', 255)->comment('Depo adı');
            $table->string('authorized_person', 255)->nullable()->comment('Yetkili kişi');
            $table->string('phone', 50)->nullable()->comment('Telefon numarası');
            $table->string('email', 255)->nullable()->comment('E-posta adresi');
            $table->char('country_code', 2)->nullable()->comment('Ülke kodu (ISO 3166-1 alpha-2)');
            $table->string('city', 100)->nullable()->comment('Şehir');
            $table->string('district', 100)->nullable()->comment('İlçe');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->text('address')->nullable()->comment('Açık adres');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Enlem koordinatı');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Boylam koordinatı');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->boolean('is_active')->default(true)->index()->comment('Aktif mi?');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            
            $table->index(['is_active', 'country_code', 'deleted_at'], 'idx_warehouse_location_status');
            $table->index(['code', 'deleted_at'], 'idx_warehouse_code_lookup');
            $table->index(['name', 'deleted_at'], 'idx_warehouse_name_search');
            $table->index(['city', 'district', 'deleted_at'], 'idx_warehouse_city_district');
            $table->index(['category_id', 'is_active'], 'idx_warehouse_category_active');
            $table->index(['created_by', 'created_at'], 'idx_warehouse_created_audit');
            $table->index(['updated_by', 'updated_at'], 'idx_warehouse_updated_audit');
            $table->index('deleted_at', 'idx_warehouse_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_WAREHOUSE);
        
        Schema::dropIfExists(self::TABLE_REVIEW);

        Schema::dropIfExists(self::TABLE_DOWNLOAD_TRANSLATION);
        Schema::dropIfExists(self::TABLE_DOWNLOAD);
        
        Schema::dropIfExists(self::TABLE_PRODUCT_DOCUMENT_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRODUCT_DOCUMENT);
        Schema::dropIfExists(self::TABLE_PRODUCT_RECURRING);
        Schema::dropIfExists(self::TABLE_PRODUCT_BUNDLE);
        Schema::dropIfExists(self::TABLE_PRODUCT_GROUPED);
        Schema::dropIfExists(self::TABLE_PRODUCT_VARIANT_VARIABLE);
        Schema::dropIfExists(self::TABLE_PRODUCT_VARIANT_STOCK);
        Schema::dropIfExists(self::TABLE_PRODUCT_VARIANT);
        Schema::dropIfExists(self::TABLE_PRODUCT_OPTION_VALUE);
        Schema::dropIfExists(self::TABLE_PRODUCT_OPTION);
        Schema::dropIfExists(self::TABLE_PRODUCT_FILTER_VALUE);
        Schema::dropIfExists(self::TABLE_PRODUCT_FIELD_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRODUCT_FIELD);
        Schema::dropIfExists(self::TABLE_PRODUCT_ATTRIBUTE_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRODUCT_ATTRIBUTE);
        Schema::dropIfExists(self::TABLE_PRODUCT_FAQ);
        Schema::dropIfExists(self::TABLE_PRODUCT_DOWNLOAD);
        Schema::dropIfExists(self::TABLE_PRODUCT_ACCESSORY);
        Schema::dropIfExists(self::TABLE_PRODUCT_POST);
        Schema::dropIfExists(self::TABLE_PRODUCT_RELATED);
        Schema::dropIfExists(self::TABLE_PRODUCT_VIDEO);
        Schema::dropIfExists(self::TABLE_PRODUCT_IMAGE);
        Schema::dropIfExists(self::TABLE_PRODUCT_REWARD);
        Schema::dropIfExists(self::TABLE_PRODUCT_ACTIVITY);
        Schema::dropIfExists(self::TABLE_PRODUCT_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRODUCT_ACCOUNT_PRICE);
        Schema::dropIfExists(self::TABLE_PRODUCT_PRODUCT);
    }
};
