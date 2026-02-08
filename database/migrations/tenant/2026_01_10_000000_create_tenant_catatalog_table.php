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

    private const TABLE_PRODUCT = 'cat_product';

    private const TABLE_PRODUCT_TRANSLATION = 'cat_product_translation';

    private const TABLE_PRODUCT_IMAGE = 'cat_product_image';

    private const TABLE_PRODUCT_VIDEO = 'cat_product_video';

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

    private const TABLE_PRODUCT_RELATED = 'cat_product_related';

    private const TABLE_PRODUCT_ACCESSORY = 'cat_product_accessory';

    private const TABLE_PRODUCT_POST = 'cat_product_post';

    private const TABLE_PRODUCT_FAQ = 'cat_product_faq';

    private const TABLE_PRODUCT_DOCUMENT = 'cat_product_document';

    private const TABLE_PRODUCT_DOCUMENT_TRANSLATION = 'cat_product_document_translation';

    private const TABLE_PRODUCT_RECURRING = 'cat_product_recurring';

    private const TABLE_PRODUCT_ACCOUNT_PRICE = 'cat_product_account_price';

    private const TABLE_PRODUCT_REWARD = 'cat_product_reward';

    private const TABLE_PRODUCT_ACTIVITY = 'cat_product_activity';

    private const TABLE_DOWNLOAD = 'cat_download';

    private const TABLE_DOWNLOAD_TRANSLATION = 'cat_download_translation';

    private const TABLE_PRODUCT_DOWNLOAD = 'cat_product_download';

    private const TABLE_WAREHOUSE = 'cat_warehouse';

    private const TABLE_REVIEW = 'cat_review';

    public function up(): void
    {
        Schema::create(self::TABLE_PRODUCT, function (Blueprint $table) {
            $table->comment('Ürün kataloğunun temel tablosudur. Tüm ürün türleri (fiziksel, dijital, hizmet) için ana kayıtları tutar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_id')->comment('Ürün benzersiz kayıt kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Mağaza içi benzersiz ürün kodu');
            $table->string('sku', 100)->unique()->nullable()->comment('Stok Takip Kodu (SKU)');
            $table->string('barcode', 100)->nullable()->index()->comment('EAN, UPC vb. barkod numarası');
            $table->string('model', 150)->nullable()->comment('Üretici model numarası veya adı');
            $table->unsignedInteger('type_id')->default(1)->comment('Ürün Tipi');
            $table->unsignedInteger('condition_id')->default(1)->comment('Ürün Durumu');
            $table->unsignedBigInteger('category_id')->nullable()->index()->comment('Varsayılan ana kategori kimliği');
            $table->unsignedBigInteger('brand_id')->nullable()->index()->comment('Üretici marka kimliği');
            $table->decimal('buy_price', 19, 4)->default(0)->comment('Ürün maliyet/alış fiyatı');
            $table->char('buy_currency_code', 3)->default('TRY')->comment('Alış para birimi');
            $table->unsignedBigInteger('buy_tax_class_id')->default(0)->comment('Alış vergi sınıfı');
            $table->decimal('sell_price', 19, 4)->default(0)->index()->comment('Liste satış fiyatı');
            $table->char('sell_currency_code', 3)->default('TRY')->comment('Satış para birimi');
            $table->unsignedBigInteger('sell_tax_class_id')->default(0)->comment('Satış vergi sınıfı');
            $table->decimal('discount_value', 19, 4)->default(0)->comment('Varsayılan indirim miktarı');
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed')->comment('İndirim türü');
            $table->unsignedBigInteger('variant_stock_id')->default(0)->comment('Varsayılan varyant stok ID');
            $table->unsignedInteger('quantity')->default(0)->comment('Mevcut stok adedi');
            $table->unsignedInteger('minimum_quantity')->default(1)->comment('Sepete eklenebilir minimum adet');
            $table->unsignedInteger('maximum_quantity')->default(0)->comment('Sepete eklenebilir maksimum adet');
            $table->boolean('subtract_stock')->default(true)->comment('Siparişte stok düşülsün mü?');
            $table->unsignedInteger('stockless_id')->default(0)->comment('Stok bittiğinde davranış ID');
            $table->decimal('weight', 10, 3)->default(0)->comment('Ağırlık');
            $table->unsignedInteger('weight_class_id')->default(0)->comment('Ağırlık birimi');
            $table->decimal('length', 10, 2)->default(0)->comment('Uzunluk');
            $table->decimal('width', 10, 2)->default(0)->comment('Genişlik');
            $table->decimal('height', 10, 2)->default(0)->comment('Yükseklik');
            $table->unsignedInteger('length_class_id')->default(0)->comment('Uzunluk birimi');
            $table->decimal('desi', 10, 2)->default(0)->comment('Kargo hacim (desi) değeri');
            $table->unsignedInteger('warranty_period')->default(0)->comment('Garanti süresi');
            $table->enum('warranty_type', ['day', 'week', 'month', 'year'])->nullable()->comment('Garanti süre tipi');
            $table->unsignedInteger('delivery_time')->default(0)->comment('Tahmini teslimat süresi');
            $table->decimal('cargo_price', 19, 4)->default(0)->comment('Ekstra kargo ücreti');
            $table->string('image_cover', 500)->nullable()->comment('Liste görünümü kapak görseli');
            $table->string('image_hover', 500)->nullable()->comment('Liste görünümü hover görseli');
            $table->string('video_cover', 500)->nullable()->comment('Liste görünümü kapak videosu');
            $table->unsignedBigInteger('viewed')->default(0)->index()->comment('Toplam görüntülenme sayısı');
            $table->unsignedInteger('sort_order')->default(0)->index()->comment('Sıralama değeri');
            $table->unsignedInteger('layout_id')->default(0)->comment('Özel sayfa şablonu ID');
            $table->boolean('is_domestic')->default(true)->comment('Yerli Üretim rozeti');
            $table->boolean('is_adult')->default(false)->index()->comment('Yetişkin içerik');
            $table->boolean('requires_shipping')->default(true)->comment('Fiziksel gönderim gerektirir mi?');
            $table->boolean('requires_membership')->default(false)->comment('Sadece üyelere mi özel?');
            $table->boolean('installment_enabled')->default(true)->comment('Taksit seçenekleri aktif mi?');
            $table->boolean('status')->default(true)->index()->comment('Ürün genel durumu');
            $table->boolean('is_published')->default(false)->index()->comment('Yayında mı?');
            $table->timestamp('available_at')->nullable()->comment('Satışa açılma tarihi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');

            $table->index(['category_id', 'is_published', 'status', 'sort_order'], 'idx_prod_cat_list');
            $table->index(['brand_id', 'is_published', 'status'], 'idx_prod_brand_list');
            $table->index(['is_published', 'status', 'created_at'], 'idx_prod_new_arrivals');
            $table->index('deleted_at', 'idx_prod_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürünlerin dile özel metin içeriklerini (Ad, açıklama, SEO) barındırır.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->comment('Bağlı olduğu ürün ID');
            $table->char('language_code', 5)->comment('Dil kodu (tr, en)');
            $table->string('name', 500)->index()->comment('Ürün adı');
            $table->string('slug', 600)->unique()->comment('SEO dostu URL');
            $table->string('summary', 500)->nullable()->comment('Kısa açıklama / Özet');
            $table->longText('description')->nullable()->comment('Detaylı ürün açıklaması (HTML)');
            $table->string('tag', 1000)->nullable()->comment('Virgülle ayrılmış etiketler');
            $table->string('meta_title', 255)->nullable()->comment('SEO Başlık');
            $table->text('meta_description')->nullable()->comment('SEO Açıklama');
            $table->string('meta_keyword', 500)->nullable()->comment('SEO Anahtar Kelimeler');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['product_id', 'language_code'], 'idx_prod_lang_unique');
            $table->fullText(['name', 'description'], 'idx_prod_fulltext');
            $table->index('deleted_at', 'idx_prod_trans_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_IMAGE, function (Blueprint $table) {
            $table->comment('Ürün galeri görsellerini tutar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_image_id')->comment('Görsel benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Bağlı olduğu ürün ID');
            $table->string('file', 500)->comment('Görsel dosya yolu');
            $table->string('title', 255)->nullable()->comment('Görsel başlığı');
            $table->string('alt', 255)->nullable()->comment('Alternatif metin');
            $table->unsignedInteger('sort_order')->default(0)->comment('Görüntüleme sırası');
            $table->boolean('is_cover')->default(false)->comment('Bu görsel kapak mı?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            $table->index('deleted_at', 'idx_prod_img_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_VIDEO, function (Blueprint $table) {
            $table->comment('Ürün tanıtım videolarını tutar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_video_id')->comment('Video benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Bağlı olduğu ürün ID');
            $table->enum('source', ['youtube', 'vimeo', 'file', 'embed'])->default('youtube')->comment('Video kaynağı');
            $table->string('content', 1000)->comment('Video ID veya URL');
            $table->string('thumbnail', 500)->nullable()->comment('Video önizleme görseli');
            $table->unsignedInteger('sort_order')->default(0)->comment('Görüntüleme sırası');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            $table->index('deleted_at', 'idx_prod_vid_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ATTRIBUTE, function (Blueprint $table) {
            $table->comment('Ürüne tanımlanan teknik özellikler.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_attribute_id')->comment('Özellik benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Bağlı olduğu ürün ID');
            $table->unsignedBigInteger('attribute_id')->comment('Global özellik tanım ID\'si');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['product_id', 'attribute_id'], 'idx_prod_attr_lookup');
            $table->index('deleted_at', 'idx_prod_attr_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ATTRIBUTE_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün özellik değerlerinin dillere göre karşılığı.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_attribute_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_attribute_id')->comment('Bağlı özellik ID');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->text('text')->comment('Özellik değeri');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['product_attribute_id', 'language_code'], 'idx_prod_attr_trans_unique');
            $table->index('deleted_at', 'idx_prod_attr_trans_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_FIELD, function (Blueprint $table) {
            $table->comment('Ürüne özel ekstra bilgi alanları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_field_id')->comment('Özel alan benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Bağlı olduğu ürün ID');
            $table->unsignedInteger('field_type_id')->comment('Alan tipi ID');
            $table->string('image', 500)->nullable()->comment('Alan görseli');
            $table->unsignedInteger('sort_order')->default(0)->comment('Görüntüleme sırası');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            $table->index('deleted_at', 'idx_prod_field_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_FIELD_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün özel alan değerlerinin çevirileri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_field_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_field_id')->comment('Bağlı alan ID');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Alan etiketi');
            $table->text('value')->nullable()->comment('Alan değeri');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['product_field_id', 'language_code'], 'idx_prod_field_trans_unique');
            $table->index('deleted_at', 'idx_prod_field_trans_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_FILTER_VALUE, function (Blueprint $table) {
            $table->comment('Ürünün filtreleme değerleri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_filter_value_id')->comment('Benzersiz ilişki kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Bağlı olduğu ürün ID');
            $table->unsignedBigInteger('filter_value_id')->index()->comment('Seçili filtre değeri ID');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['product_id', 'filter_value_id'], 'idx_prod_filter_unique');
            $table->index('deleted_at', 'idx_prod_filter_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_OPTION, function (Blueprint $table) {
            $table->comment('Ürünün satın alırken seçilebilir opsiyonları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_option_id')->comment('Seçenek benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Bağlı olduğu ürün ID');
            $table->unsignedBigInteger('option_id')->comment('Global opsiyon tanım ID');
            $table->boolean('required')->default(false)->comment('Seçim zorunlu mu?');
            $table->string('value', 255)->nullable()->comment('Varsayılan metin değeri');
            $table->unsignedInteger('sort_order')->default(0)->comment('Görüntüleme sırası');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            $table->index('deleted_at', 'idx_prod_opt_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_OPTION_VALUE, function (Blueprint $table) {
            $table->comment('Opsiyonların alt değerleri ve etkileri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_option_value_id')->comment('Değer benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_option_id')->index()->comment('Bağlı ürün seçeneği ID');
            $table->unsignedBigInteger('option_value_id')->comment('Global değer ID');
            $table->integer('quantity')->default(0)->comment('Opsiyon stoğu');
            $table->boolean('subtract_stock')->default(false)->comment('Ürün stoğundan düşülsün mü?');
            $table->decimal('price_modifier', 19, 4)->default(0)->comment('Fiyat farkı');
            $table->char('price_modifier_type', 1)->default('+')->comment('Fiyat farkı tipi');
            $table->decimal('weight_modifier', 10, 3)->default(0)->comment('Ağırlık farkı');
            $table->char('weight_modifier_type', 1)->default('+')->comment('Ağırlık farkı tipi');
            $table->unsignedInteger('sort_order')->default(0)->comment('Görüntüleme sırası');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            $table->index('deleted_at', 'idx_prod_opt_val_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_VARIANT, function (Blueprint $table) {
            $table->comment('Ürün varyant grupları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_variant_id')->comment('Varyant grubu ilişki kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Bağlı olduğu ürün ID');
            $table->string('name', 255)->comment('Varyant Grup Adı');
            $table->unsignedInteger('variant_id')->comment('Global varyant ID');
            $table->unsignedInteger('sort_order')->default(0)->comment('Görüntüleme sırası');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');
            $table->index('deleted_at', 'idx_prod_var_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_VARIANT_STOCK, function (Blueprint $table) {
            $table->comment('Varyant kombinasyonlarının stok ve fiyat bilgileri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_variant_stock_id')->comment('Stok kombinasyon kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ana ürün ID');
            $table->string('sku', 100)->unique()->comment('Varyant stok kodu');
            $table->string('barcode', 100)->nullable()->comment('Barkod');
            $table->integer('quantity')->default(0)->index()->comment('Varyant stoğu');
            $table->decimal('price', 19, 4)->default(0)->comment('Kombinasyon fiyatı');
            $table->string('image', 500)->nullable()->comment('Özel görsel');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_var_stock_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_VARIANT_VARIABLE, function (Blueprint $table) {
            $table->comment('Stok kombinasyonunun değerleri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_variant_variable_id')->comment('Eşleştirme kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_variant_stock_id')->index()->comment('Bağlı stok kaydı ID');
            $table->unsignedBigInteger('product_id')->comment('Ürün ID');
            $table->unsignedBigInteger('variant_id')->comment('Varyant ID');
            $table->unsignedBigInteger('variant_value_id')->comment('Varyant değer ID');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['product_variant_stock_id', 'variant_id'], 'idx_var_stock_def');
            $table->index('deleted_at', 'idx_prod_var_variable_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_GROUPED, function (Blueprint $table) {
            $table->comment('Gruplu ürün yapısı.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_grouped_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->comment('Ana (Grup) Ürün');
            $table->unsignedBigInteger('grouped_id')->comment('Bağlı alt ürün');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['product_id', 'grouped_id'], 'idx_prod_grouped_uniq');
            $table->index('deleted_at', 'idx_prod_grouped_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_BUNDLE, function (Blueprint $table) {
            $table->comment('Paket/Set ürün yapısı.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_bundle_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->comment('Paket Ürün');
            $table->unsignedBigInteger('bundle_id')->comment('Paket içindeki ürün');
            $table->unsignedInteger('quantity')->default(1)->comment('Paketteki adet');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_bundle_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_RELATED, function (Blueprint $table) {
            $table->comment('Benzer ürün tavsiyeleri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_related_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ana ürün');
            $table->unsignedBigInteger('related_id')->comment('Tavsiye edilen ürün');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['product_id', 'related_id'], 'idx_prod_rel_unique');
            $table->index('deleted_at', 'idx_prod_rel_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ACCESSORY, function (Blueprint $table) {
            $table->comment('Tamamlayıcı aksesuar ürünleri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_accessory_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ana ürün');
            $table->unsignedBigInteger('accessory_id')->comment('Aksesuar ürün');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_acc_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_POST, function (Blueprint $table) {
            $table->comment('Ürünle ilişkili blog yazıları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_post_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ürün ID');
            $table->unsignedBigInteger('post_id')->comment('Yazı ID');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_post_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_FAQ, function (Blueprint $table) {
            $table->comment('Ürünle ilişkili Sıkça Sorulan Sorular.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_faq_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ürün ID');
            $table->unsignedBigInteger('faq_id')->comment('Soru ID');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_faq_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_DOCUMENT, function (Blueprint $table) {
            $table->comment('Ürün dokümanları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_document_id')->comment('Doküman benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Bağlı ürün ID');
            $table->string('file', 500)->comment('Dosya yolu');
            $table->string('filename', 255)->comment('Orijinal dosya adı');
            $table->unsignedInteger('sort_order')->default(0)->comment('Sıralama');
            $table->boolean('status')->default(true)->comment('Aktiflik durumu');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_doc_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_DOCUMENT_TRANSLATION, function (Blueprint $table) {
            $table->comment('Ürün doküman açıklamalarının çevirisi.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_document_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_document_id')->comment('Bağlı doküman ID');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Doküman görünen adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_doc_trans_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_RECURRING, function (Blueprint $table) {
            $table->comment('Abonelikli ürünler için tekrarlı ödeme planı eşleşmesi.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_recurring_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ürün ID');
            $table->unsignedBigInteger('recurring_id')->comment('Ödeme periyodu ID');
            $table->unsignedBigInteger('account_group_id')->comment('Hangi müşteri grubu için');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_rec_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ACCOUNT_PRICE, function (Blueprint $table) {
            $table->comment('Müşteri gruplarına özel fiyat tanımları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_account_price_id')->comment('Fiyat tanımlama kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ürün ID');
            $table->unsignedBigInteger('account_group_id')->comment('Müşteri grubu ID');
            $table->decimal('price', 19, 4)->comment('Özel fiyat');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->unique(['product_id', 'account_group_id'], 'idx_prod_acc_price_unique');
            $table->index('deleted_at', 'idx_prod_acc_price_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_REWARD, function (Blueprint $table) {
            $table->comment('Ürünü satın alanlara verilecek ödül puanları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_reward_id')->comment('Ödül benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ürün ID');
            $table->unsignedBigInteger('account_group_id')->comment('Müşteri grubu ID');
            $table->unsignedInteger('points')->default(0)->comment('Kazanılacak puan');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_reward_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_ACTIVITY, function (Blueprint $table) {
            $table->comment('Ürün ile ilgili işlem logları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_activity_id')->comment('Aktivite benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ürün ID');
            $table->unsignedBigInteger('account_id')->default(0)->comment('İşlemi yapan kullanıcı');
            $table->string('action', 50)->comment('İşlem türü');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->decimal('price', 19, 4)->default(0)->comment('İşlem anındaki fiyat');
            $table->integer('quantity_change')->default(0)->comment('Stok değişim miktarı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_act_soft_delete');
        });

        Schema::create(self::TABLE_DOWNLOAD, function (Blueprint $table) {
            $table->comment('Satılabilir veya indirilebilir dijital dosya tanımları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('download_id')->comment('Dosya benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Dosya kodu');
            $table->string('filename', 500)->comment('Disk üzerindeki dosya adı');
            $table->string('mask', 255)->nullable()->comment('Kullanıcıya gösterilecek dosya adı');
            $table->unsignedInteger('download_count')->default(0)->comment('İndirilme sayısı');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_dl_soft_delete');
        });

        Schema::create(self::TABLE_DOWNLOAD_TRANSLATION, function (Blueprint $table) {
            $table->comment('Dijital dosya adlarının çevirisi.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('download_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('download_id')->comment('Bağlı dosya ID');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Dosya görünen adı');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_dl_trans_soft_delete');
        });

        Schema::create(self::TABLE_PRODUCT_DOWNLOAD, function (Blueprint $table) {
            $table->comment('Ürün ile dijital dosya ilişkisi.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('product_download_id')->comment('İlişki benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Ürün ID');
            $table->unsignedBigInteger('download_id')->comment('Dosya ID');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_prod_dl_soft_delete');
        });

        Schema::create(self::TABLE_WAREHOUSE, function (Blueprint $table) {
            $table->comment('Fiziksel depo tanımları.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('warehouse_id')->comment('Depo benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('Depo kodu');
            $table->string('name', 255)->comment('Depo adı');
            $table->unsignedInteger('country_id')->default(0)->comment('Ülke ID');
            $table->unsignedInteger('city_id')->default(0)->comment('Şehir ID');
            $table->text('address')->nullable()->comment('Açık adres');
            $table->boolean('status')->default(true)->index()->comment('Durum');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_warehouse_soft_delete');
        });

        Schema::create(self::TABLE_REVIEW, function (Blueprint $table) {
            $table->comment('Ürün yorum ve değerlendirmeleri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('review_id')->comment('Yorum benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('product_id')->index()->comment('Yorum yapılan ürün');
            $table->unsignedBigInteger('account_id')->default(0)->index()->comment('Yorum yapan müşteri (0:Misafir)');
            $table->string('author', 100)->comment('Görünen isim');
            $table->text('text')->comment('Yorum içeriği');
            $table->unsignedTinyInteger('rating')->default(0)->comment('Puan (1-5)');
            $table->boolean('status')->default(false)->index()->comment('Onay durumu (False:Onay Bekliyor)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['product_id', 'status'], 'idx_review_prod_status');
            $table->index('deleted_at', 'idx_review_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_REVIEW);
        Schema::dropIfExists(self::TABLE_WAREHOUSE);
        Schema::dropIfExists(self::TABLE_PRODUCT_DOWNLOAD);
        Schema::dropIfExists(self::TABLE_DOWNLOAD_TRANSLATION);
        Schema::dropIfExists(self::TABLE_DOWNLOAD);
        Schema::dropIfExists(self::TABLE_PRODUCT_ACTIVITY);
        Schema::dropIfExists(self::TABLE_PRODUCT_REWARD);
        Schema::dropIfExists(self::TABLE_PRODUCT_ACCOUNT_PRICE);
        Schema::dropIfExists(self::TABLE_PRODUCT_RECURRING);
        Schema::dropIfExists(self::TABLE_PRODUCT_DOCUMENT_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRODUCT_DOCUMENT);
        Schema::dropIfExists(self::TABLE_PRODUCT_FAQ);
        Schema::dropIfExists(self::TABLE_PRODUCT_POST);
        Schema::dropIfExists(self::TABLE_PRODUCT_ACCESSORY);
        Schema::dropIfExists(self::TABLE_PRODUCT_RELATED);
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
        Schema::dropIfExists(self::TABLE_PRODUCT_VIDEO);
        Schema::dropIfExists(self::TABLE_PRODUCT_IMAGE);
        Schema::dropIfExists(self::TABLE_PRODUCT_TRANSLATION);
        Schema::dropIfExists(self::TABLE_PRODUCT);
    }
};
