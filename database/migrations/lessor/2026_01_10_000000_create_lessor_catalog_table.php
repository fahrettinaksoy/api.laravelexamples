<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_lsr';

    private const PRODUCT_TABLE = 'ctlg_product';

    private const PRODUCT_TRANSLATION_TABLE = 'ctlg_product_translation';

    private const PRODUCT_GROUPED_TABLE = 'ctlg_product_grouped';

    private const PRODUCT_BUNDLE_TABLE = 'ctlg_product_bundle';

    private const PRODUCT_RECURRING_TABLE = 'ctlg_product_recurring';

    private const PRODUCT_IMAGE_TABLE = 'ctlg_product_image';

    private const PRODUCT_VIDEO_TABLE = 'ctlg_product_video';

    private const PRODUCT_FAQ_TABLE = 'ctlg_product_faq';

    private const PRODUCT_RELATED_TABLE = 'ctlg_product_related';

    private const PRODUCT_POST_TABLE = 'ctlg_product_post';

    private const PRODUCT_ATTRIBUTE_TABLE = 'ctlg_product_attribute';

    private const PRODUCT_ATTRIBUTE_TRANSLATION_TABLE = 'ctlg_product_attribute_translation';

    private const PRODUCT_FIELD_TABLE = 'ctlg_product_field';

    private const PRODUCT_FIELD_TRANSLATION_TABLE = 'ctlg_product_field_translation';

    private const PRODUCT_FILTER_VALUE_TABLE = 'ctlg_product_filter_value';

    private const PRODUCT_OPTION_TABLE = 'ctlg_product_option';

    private const PRODUCT_OPTION_VALUE_TABLE = 'ctlg_product_option_value';

    private const PRODUCT_VARIANT_TABLE = 'ctlg_product_variant';

    private const PRODUCT_VARIANT_STOCK_TABLE = 'ctlg_product_variant_stock';

    private const PRODUCT_VARIANT_VARIABLE_TABLE = 'ctlg_product_variant_variable';

    private const PRODUCT_ACTIVITY_TABLE = 'ctlg_product_activity';

    private const REVIEW_TABLE = 'ctlg_review';

    private const CATEGORY_TABLE = 'ctlg_category';

    private const CATEGORY_TRANSLATION_TABLE = 'ctlg_category_translation';

    private const BRAND_TABLE = 'ctlg_brand';

    private const BRAND_TRANSLATION_TABLE = 'ctlg_brand_translation';

    public function up(): void
    {
        Schema::create(self::PRODUCT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlerin temel bilgilerini saklar.');

            $table->bigIncrements('product_id')->comment('Ürün için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Ürün için benzersiz kod/SKU (genel)');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Ürün tipi ID\'si (örn: Basit, Varyantlı, Dijital)');
            $table->unsignedBigInteger('variant_stock_id')->default(0)->nullable()->comment('Ürün varyant stoğu ID\'si (eğer ürün ana ürün ise varsayılan varyant stoğu)');
            $table->unsignedBigInteger('category_id')->default(0)->comment('Ürünün ana kategori ID\'si');
            $table->unsignedBigInteger('brand_id')->default(0)->nullable()->comment('Ürünün marka ID\'si');
            $table->string('model', 100)->nullable()->comment('Ürün modeli');
            $table->string('sku', 100)->unique()->nullable()->comment('Ürünün stok kodu/SKU (global benzersiz)');
            $table->string('image_cover', 255)->nullable()->comment('Ürünün ana kapak görselinin URL veya dosya yolu');
            $table->string('image_hover', 255)->nullable()->comment('Ürünün fare üzerine gelindiğinde gösterilecek görselin URL veya dosya yolu');
            $table->string('video_cover', 255)->nullable()->comment('Ürünün kapak videosunun URL veya dosya yolu');
            $table->string('live_broadcast_url', 500)->nullable()->comment('Canlı yayın bağlantısı URL\'si');

            $table->decimal('buy_price', 19, 4)->comment('Ürünün alış fiyatı');
            $table->string('buy_currency_code', 3)->comment('Alış fiyatının para birimi kodu');
            $table->unsignedBigInteger('buy_tax_class_id')->default(0)->nullable()->comment('Alış fiyatına uygulanan vergi sınıfı ID\'si');
            $table->decimal('sell_price', 19, 4)->comment('Ürünün satış fiyatı');
            $table->string('sell_currency_code', 3)->comment('Satış fiyatının para birimi kodu');
            $table->unsignedBigInteger('sell_tax_class_id')->default(0)->nullable()->comment('Satış fiyatına uygulanan vergi sınıfı ID\'si');
            $table->integer('sell_point')->default(0)->comment('Ürünün satışından kazanılan puan miktarı');
            $table->decimal('discount_value', 19, 4)->default(0.0000)->comment('Ürüne uygulanan indirim değeri');
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->comment('İndirim tipi (percentage veya amount)');
            $table->integer('point_reward')->default(0)->comment('Ürünü satın alana verilen ödül puanı');

            $table->integer('sort_order')->default(0)->comment('Ürünlerin listeleme sırası');
            $table->tinyInteger('status_usage')->default(0)->comment('Ürünün kullanım durumu (örn: 0: Normal, 1: Özel Kullanım)');
            $table->boolean('status_publishing')->default(false)->comment('Ürünün yayın durumu (0: Taslak, 1: Yayınlandı, 2: Yayından Kaldırıldı)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_ctlg_prod_code');
            $table->index('sku', 'idx_ctlg_prod_sku');
            $table->index('type_id', 'idx_ctlg_prod_type_id');
            $table->index('category_id', 'idx_ctlg_prod_cat_id');
            $table->index('brand_id', 'idx_ctlg_prod_brand_id');
            $table->index('status_publishing', 'idx_ctlg_prod_pub_status');
            $table->index('sort_order', 'idx_ctlg_prod_sort_order');
        });

        Schema::create(self::PRODUCT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('product_translation_id')->comment('Ürün çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)');
            $table->string('name', 255)->comment('Ürünün çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Ürünün çevrilmiş kısa özeti');
            $table->text('description')->nullable()->comment('Ürünün çevrilmiş detaylı açıklaması');
            $table->string('tag', 255)->nullable()->comment('Ürünün çevrilmiş etiketleri (virgülle ayrılmış)');
            $table->string('keyword', 255)->nullable()->comment('Ürünün SEO için çevrilmiş anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_trans_prod_id');
            $table->index('language_code', 'idx_ctlg_prod_trans_lang_code');
            $table->unique(['product_id', 'language_code'], 'idx_ctlg_prod_trans_unique_lang');
        });

        Schema::create(self::PRODUCT_GROUPED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Grup olarak satılan ürünlerde, ana ürüne bağlı alt ürünleri saklar.');

            $table->bigIncrements('product_grouped_id')->comment('Gruplanmış ürün ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('Ana ürün ID\'si');
            $table->unsignedBigInteger('grouped_id')->comment('Gruplanmış (alt) ürün ID\'si');
            $table->unsignedInteger('opening')->default(0)->comment('Grubun açılış/başlangıç miktarı (varsa)');
            $table->unsignedInteger('minimum')->default(0)->comment('Grubun minimum satın alma miktarı');
            $table->unsignedInteger('maximum')->default(0)->comment('Grubun maksimum satın alma miktarı');
            $table->decimal('coefficient', 15, 4)->default(1.0)->comment('Gruplanmış ürünün fiyat katsayısı');
            $table->integer('sort_order')->default(0)->comment('Gruplanmış ürünlerin gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_grouped_prod_id');
            $table->index('grouped_id', 'idx_ctlg_prod_grouped_grouped_id');
            $table->unique(['product_id', 'grouped_id'], 'idx_ctlg_prod_grouped_unique');
        });

        Schema::create(self::PRODUCT_BUNDLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün paketlerini (bundle) ve bu paketlerin içerdiği ürünleri saklar.');

            $table->bigIncrements('product_bundle_id')->comment('Ürün paketi ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('Paket ürün ID\'si');
            $table->unsignedBigInteger('bundle_id')->comment('Paket içindeki ürün ID\'si');
            $table->unsignedInteger('quantity')->default(0)->comment('Paket içindeki ürün miktarı');
            $table->integer('sort_order')->default(0)->comment('Paket içindeki ürünlerin gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_bundle_prod_id');
            $table->index('bundle_id', 'idx_ctlg_prod_bundle_bundle_id');
            $table->unique(['product_id', 'bundle_id'], 'idx_ctlg_prod_bundle_unique');
        });

        Schema::create(self::PRODUCT_RECURRING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlerin tekrarlayan (abonelik) fiyatlandırma bilgilerini saklar.');

            $table->bigIncrements('product_recurring_id')->comment('Ürün tekrarlayan fiyat kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('recurring_type_id')->default(0)->comment('Tekrarlayan ödeme tipi ID\'si (örn: Aylık, Yıllık)');
            $table->unsignedBigInteger('account_type_id')->default(0)->comment('Uygulanacağı hesap tipi ID\'si (örn: Bireysel, Kurumsal)');
            $table->boolean('default')->default(false)->comment('Varsayılan tekrarlayan seçenek mi? (0: Hayır, 1: Evet)');
            $table->decimal('price', 19, 4)->comment('Tekrarlayan ödeme fiyatı');
            $table->string('currency_code', 3)->comment('Para birimi kodu');
            $table->unsignedBigInteger('tax_class_id')->default(0)->nullable()->comment('Uygulanacak vergi sınıfı ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_rec_prod_id');
            $table->index('recurring_type_id', 'idx_ctlg_prod_rec_rec_type_id');
            $table->index('account_type_id', 'idx_ctlg_prod_rec_acc_type_id');
            $table->unique(['product_id', 'recurring_type_id', 'account_type_id'], 'idx_ctlg_prod_rec_unique_option');
        });

        Schema::create(self::PRODUCT_IMAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlere ait ek görselleri saklar.');

            $table->bigIncrements('product_image_id')->comment('Ürün görseli için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->string('file', 255)->comment('Görsel dosyasının URL veya dosya yolu');
            $table->string('description', 500)->nullable()->comment('Görsel açıklaması');
            $table->integer('sort_order')->default(0)->comment('Görsellerin gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_img_prod_id');
            $table->index('sort_order', 'idx_ctlg_prod_img_sort_order');
        });

        Schema::create(self::PRODUCT_VIDEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlere ait videoları saklar.');

            $table->bigIncrements('product_video_id')->comment('Ürün videosu için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->enum('source', ['code', 'url', 'file', 'embed'])->comment('Video kaynağı tipi (örn: YouTube embed kodu, doğrudan URL)');
            $table->text('content')->nullable()->comment('Video içeriği (URL, embed kodu veya dosya yolu)');
            $table->string('name', 255)->nullable()->comment('Videonun adı veya açıklaması');
            $table->integer('sort_order')->default(0)->comment('Videoların gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_vid_prod_id');
            $table->index('source', 'idx_ctlg_prod_vid_source');
            $table->index('sort_order', 'idx_ctlg_prod_vid_sort_order');
        });

        Schema::create(self::PRODUCT_FAQ_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünler ile Sıkça Sorulan Sorular (FAQ) ilişkilerini saklar.');

            $table->bigIncrements('product_faq_id')->comment('Ürün-FAQ ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('faq_id')->comment('İlgili FAQ ID\'si (supp_faq.faq_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_faq_prod_id');
            $table->index('faq_id', 'idx_ctlg_prod_faq_faq_id');
            $table->unique(['product_id', 'faq_id'], 'idx_ctlg_prod_faq_unique');
        });

        Schema::create(self::PRODUCT_RELATED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünler arası ilişkiyi (ilgili ürünler) saklar.');

            $table->bigIncrements('product_related_id')->comment('İlgili ürün ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('Ana ürün ID\'si');
            $table->unsignedBigInteger('related_id')->comment('İlgili ürün ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_rel_prod_id');
            $table->index('related_id', 'idx_ctlg_prod_rel_related_id');
            $table->unique(['product_id', 'related_id'], 'idx_ctlg_prod_rel_unique');
        });

        Schema::create(self::PRODUCT_POST_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünler ile blog gönderileri arasındaki ilişkiyi saklar.');

            $table->bigIncrements('product_post_id')->comment('Ürün-Blog gönderisi ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('post_id')->comment('İlgili blog gönderisi ID\'si (site_blog_post.post_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_post_prod_id');
            $table->index('post_id', 'idx_ctlg_prod_post_post_id');
            $table->unique(['product_id', 'post_id'], 'idx_ctlg_prod_post_unique');
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlere ait özel nitelikleri (attribute) saklar (örn: renk, boyut).');

            $table->bigIncrements('product_attribute_id')->comment('Ürün nitelik kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('attribute_variable_id')->default(0)->comment('Nitelik değişkeni ID\'si (def_cat_attribute_variable.attribute_variable_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_attr_prod_id');
            $table->index('attribute_variable_id', 'idx_ctlg_prod_attr_attr_var_id');
            $table->unique(['product_id', 'attribute_variable_id'], 'idx_ctlg_prod_attr_unique');
        });

        Schema::create(self::PRODUCT_ATTRIBUTE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün niteliklerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('product_attribute_translation_id')->comment('Ürün nitelik çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_attribute_id')->comment('İlgili ürün nitelik ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->text('text')->nullable()->comment('Nitelik değerinin çevrilmiş metni');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_attribute_id', 'idx_ctlg_prod_attr_trans_prod_attr_id');
            $table->index('language_code', 'idx_ctlg_prod_attr_trans_lang_code');
            $table->unique(['product_attribute_id', 'language_code'], 'idx_ctlg_prod_attr_trans_unique_lang');
        });

        Schema::create(self::PRODUCT_FIELD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlere ait özel alanları (custom fields) tanımlar.');

            $table->bigIncrements('product_field_id')->comment('Ürün özel alan kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('field_type_id')->default(0)->comment('Alan tipi ID\'si (örn: metin, sayı, tarih)');
            $table->string('image', 255)->nullable()->comment('Alana ait görselin URL veya dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Özel alanların gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_field_prod_id');
            $table->index('field_type_id', 'idx_ctlg_prod_field_type_id');
            $table->index('sort_order', 'idx_ctlg_prod_field_sort_order');
        });

        Schema::create(self::PRODUCT_FIELD_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün özel alanlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('product_field_translation_id')->comment('Ürün özel alan çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_field_id')->comment('İlgili ürün özel alan ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Özel alanın çevrilmiş adı/başlığı');
            $table->text('content')->nullable()->comment('Özel alanın çevrilmiş içeriği/değeri');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_field_id', 'idx_ctlg_prod_field_trans_field_id');
            $table->index('language_code', 'idx_ctlg_prod_field_trans_lang_code');
            $table->unique(['product_field_id', 'language_code'], 'idx_ctlg_prod_field_trans_unique_lang');
        });

        Schema::create(self::PRODUCT_FILTER_VALUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünler ile filtre değerleri arasındaki ilişkiyi saklar.');

            $table->bigIncrements('product_filter_value_id')->comment('Ürün-filtre değeri ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('filter_value_id')->default(0)->comment('İlgili filtre değeri ID\'si (def_cat_product_filter_value.filter_value_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_filt_val_prod_id');
            $table->index('filter_value_id', 'idx_ctlg_prod_filt_val_filt_val_id');
            $table->unique(['product_id', 'filter_value_id'], 'idx_ctlg_prod_filt_val_unique');
        });

        Schema::create(self::PRODUCT_OPTION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlere ait genel seçenekleri (option) tanımlar (örn: beden seçimi, renk seçimi).');

            $table->bigIncrements('product_option_id')->comment('Ürün seçeneği için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('option_id')->default(0)->comment('Seçenek ID\'si (def_cat_product_option.option_id)');
            $table->boolean('required')->default(false)->comment('Seçenek zorunlu mu? (0: Hayır, 1: Evet)');
            $table->integer('sort_order')->default(0)->comment('Seçeneklerin gösterim sırası');
            $table->text('value')->nullable()->comment('Seçeneğe özel değer veya ayarlar (örn: özel metin kutusu için varsayılan değer, JSON)'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_opt_prod_id');
            $table->index('option_id', 'idx_ctlg_prod_opt_opt_id');
            $table->unique(['product_id', 'option_id'], 'idx_ctlg_prod_opt_unique');
        });

        Schema::create(self::PRODUCT_OPTION_VALUE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün seçeneklerine ait değerleri (örn: beden: S, M, L) ve bunlara bağlı fiyat/stok farklılıklarını saklar.');

            $table->bigIncrements('product_option_value_id')->comment('Ürün seçenek değeri için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('product_option_id')->comment('İlgili ürün seçeneği ID\'si');
            $table->unsignedBigInteger('option_value_id')->default(0)->comment('Seçenek değeri ID\'si (def_cat_product_option_value.option_value_id)');
            $table->string('sku', 100)->nullable()->comment('Bu seçenek kombinasyonuna özel SKU');
            $table->integer('quantity')->default(0)->comment('Bu seçenek kombinasyonu için stok miktarı');
            $table->decimal('buy_price', 19, 4)->default(0.0000)->comment('Bu seçenek için ek alış fiyatı (prefix ile toplanır)');
            $table->string('buy_price_prefix', 10)->default('+')->comment('Alış fiyatına etki eden ön ek (örn: +, -, =)');
            $table->decimal('sell_price', 19, 4)->default(0.0000)->comment('Bu seçenek için ek satış fiyatı (prefix ile toplanır)');
            $table->string('sell_price_prefix', 10)->default('+')->comment('Satış fiyatına etki eden ön ek');
            $table->integer('sell_point')->default(0)->comment('Bu seçenek için ek satış puanı');
            $table->string('sell_point_prefix', 10)->default('+')->comment('Satış puanına etki eden ön ek');
            $table->decimal('weight', 15, 4)->default(0.0000)->comment('Bu seçenek için ek ağırlık değeri');
            $table->string('weight_prefix', 10)->default('+')->comment('Ağırlığa etki eden ön ek');
            $table->decimal('discount_value', 19, 4)->default(0.0000)->comment('Bu seçenek için ek indirim değeri');
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->comment('Ek indirim tipi');
            $table->integer('sort_order')->default(0)->comment('Seçenek değerlerinin gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_opt_val_prod_id');
            $table->index('product_option_id', 'idx_ctlg_prod_opt_val_prod_opt_id');
            $table->index('option_value_id', 'idx_ctlg_prod_opt_val_opt_val_id');
            $table->unique(['product_id', 'product_option_id', 'option_value_id'], 'idx_ctlg_prod_opt_val_unique');
        });

        Schema::create(self::PRODUCT_VARIANT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünler ile varyant (değişken) grupları arasındaki ilişkiyi saklar.');

            $table->bigIncrements('product_variant_id')->comment('Ürün varyant ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('variant_id')->default(0)->comment('İlgili varyant ID\'si (def_cat_product_variant.variant_id)');
            $table->integer('sort_order')->default(0)->comment('Varyantların ürün içindeki gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_var_prod_id');
            $table->index('variant_id', 'idx_ctlg_prod_var_var_id');
            $table->unique(['product_id', 'variant_id'], 'idx_ctlg_prod_var_unique');
        });

        Schema::create(self::PRODUCT_VARIANT_STOCK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün varyant kombinasyonlarına ait stok ve fiyat bilgilerini saklar.');

            $table->bigIncrements('product_variant_stock_id')->comment('Ürün varyant stok kaydı için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->string('sku', 100)->unique()->comment('Varyant kombinasyonuna özel SKU (benzersiz)');
            $table->integer('quantity')->default(0)->comment('Varyant kombinasyonu için stok miktarı');
            $table->decimal('buy_price', 19, 4)->comment('Varyant kombinasyonunun alış fiyatı');
            $table->decimal('sell_price', 19, 4)->comment('Varyant kombinasyonunun satış fiyatı');
            $table->integer('sell_point')->default(0)->comment('Varyant kombinasyonundan kazanılan puan miktarı');
            $table->decimal('weight', 15, 4)->default(0.0000)->comment('Varyant kombinasyonunun ağırlığı');
            $table->decimal('discount_value', 19, 4)->default(0.0000)->comment('Varyant kombinasyonuna uygulanan indirim değeri');
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->comment('Varyant indirim tipi');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_var_stock_prod_id');
            $table->index('sku', 'idx_ctlg_prod_var_stock_sku');
        });

        Schema::create(self::PRODUCT_VARIANT_VARIABLE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün varyant stok kayıtları ile varyant değişkenleri arasındaki ilişkiyi saklar.');

            $table->bigIncrements('product_variant_variable_id')->comment('Ürün varyant değişken ilişki kaydı için birincil anahtar'); // Orijinal adı düzeltildi
            $table->unsignedBigInteger('product_variant_stock_id')->comment('İlgili ürün varyant stok ID\'si');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('variant_id')->default(0)->comment('İlgili varyant ID\'si');
            $table->unsignedBigInteger('variant_variable_id')->default(0)->comment('İlgili varyant değişkeni ID\'si (def_cat_product_variant_variable.variant_variable_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_variant_stock_id', 'idx_ctlg_prod_var_var_stock_id');
            $table->index('product_id', 'idx_ctlg_prod_var_var_prod_id');
            $table->index('variant_id', 'idx_ctlg_prod_var_var_var_id');
            $table->index('variant_variable_id', 'idx_ctlg_prod_var_var_var_var_id');
            $table->unique(['product_variant_stock_id', 'variant_id', 'variant_variable_id'], 'idx_ctlg_prod_var_var_unique');
        });

        Schema::create(self::PRODUCT_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlerin stok hareketlerini ve diğer aktivitelerini saklar (giriş/çıkış, iade vb.).');

            $table->bigIncrements('product_activity_id')->comment('Ürün aktivitesi için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->unsignedBigInteger('entry_id')->default(0)->comment('Giriş tipi ID\'si (örn: Satın Alma, İade)');
            $table->unsignedBigInteger('relation_id')->default(0)->nullable()->comment('İlişkili modül ID\'si (örn: order_id, invoice_id)');
            $table->string('module_type', 50)->nullable()->comment('İlişkili modülün tipi (örn: order, invoice)');
            $table->unsignedBigInteger('module_id')->default(0)->nullable()->comment('İlişkili modül kaydının ID\'si');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('İlgili hesap ID\'si (işlemi yapan/ilgili)');
            $table->unsignedBigInteger('product_variant_stock_id')->default(0)->nullable()->comment('Etkilenen ürün varyant stok ID\'si (varsa)');
            $table->integer('quantity')->default(0)->comment('Etkilenen miktar (pozitif: giriş, negatif: çıkış)');
            $table->decimal('price', 19, 4)->comment('İşlem anındaki ürün fiyatı');
            $table->string('currency_code', 3)->comment('Fiyatın para birimi kodu');
            $table->unsignedBigInteger('tax_class_id')->default(0)->nullable()->comment('Uygulanan vergi sınıfı ID\'si');
            $table->string('code', 100)->nullable()->comment('Aktivite için benzersiz kod/referans');
            $table->text('description')->nullable()->comment('Aktivitenin açıklaması');
            $table->unsignedBigInteger('warehouse_id')->default(0)->nullable()->comment('İlgili depo ID\'si');
            $table->unsignedBigInteger('area_id')->default(0)->nullable()->comment('İlgili alan ID\'si (depo içinde)');
            $table->unsignedBigInteger('shelf_id')->default(0)->nullable()->comment('İlgili raf ID\'si (alan içinde)');
            $table->boolean('approval')->default(false)->comment('Aktivitenin onay durumu (0: Beklemede, 1: Onaylandı)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_prod_act_prod_id');
            $table->index('entry_id', 'idx_ctlg_prod_act_entry_id');
            $table->index('relation_id', 'idx_ctlg_prod_act_rel_id');
            $table->index('module_id', 'idx_ctlg_prod_act_mod_id');
            $table->index('account_id', 'idx_ctlg_prod_act_acc_id');
            $table->index('product_variant_stock_id', 'idx_ctlg_prod_act_var_stock_id');
            $table->index('warehouse_id', 'idx_ctlg_prod_act_wh_id');
            $table->index('approval', 'idx_ctlg_prod_act_approval');
        });

        Schema::create(self::REVIEW_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürünlere yapılan kullanıcı yorumlarını ve değerlendirmelerini saklar.');

            $table->bigIncrements('review_id')->comment('Yorum için birincil anahtar');
            $table->unsignedBigInteger('product_id')->comment('Yorumun yapıldığı ürün ID\'si');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('Yorumu yapan kullanıcı/hesap ID\'si (0 ise misafir)');
            $table->string('code', 100)->unique()->nullable()->comment('Yorum için benzersiz referans kodu');
            $table->string('author', 255)->comment('Yorumu yapan kişinin adı');
            $table->text('content')->comment('Yorumun içeriği');
            $table->unsignedTinyInteger('rating')->default(0)->comment('Yorum için verilen puan (örn: 1-5 arası)');
            $table->boolean('status')->default(false)->comment('Yorumun durumu (0: Beklemede, 1: Onaylandı, 2: Reddedildi)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('product_id', 'idx_ctlg_rev_prod_id');
            $table->index('account_id', 'idx_ctlg_rev_acc_id');
            $table->index('status', 'idx_ctlg_rev_status');
        });

        Schema::create(self::CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün kategorilerinin temel bilgilerini saklar.');

            $table->bigIncrements('category_id')->comment('Kategori için birincil anahtar');
            $table->unsignedBigInteger('parent_id')->default(0)->nullable()->comment('Üst kategoriye referans ID (0 ise ana kategori)');
            $table->string('code', 100)->unique()->comment('Kategori için benzersiz kod');
            $table->string('image', 255)->nullable()->comment('Kategori görselinin URL veya dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Kategorilerin listeleme sırası');
            $table->tinyInteger('membership')->default(0)->comment('Kategorinin üyelik durumu veya erişim düzeyi');
            $table->boolean('status')->default(false)->comment('Kategorinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('parent_id', 'idx_ctlg_cat_parent_id');
            $table->index('code', 'idx_ctlg_cat_code');
            $table->index('status', 'idx_ctlg_cat_status');
            $table->index('sort_order', 'idx_ctlg_cat_sort_order');
        });

        Schema::create(self::CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kategori bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('category_translation_id')->comment('Kategori çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('category_id')->comment('İlgili kategori ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Kategorinin çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Kategorinin çevrilmiş kısa özeti');
            $table->text('description')->nullable()->comment('Kategorinin çevrilmiş detaylı açıklaması');
            $table->string('keyword', 255)->nullable()->comment('Kategorinin SEO için çevrilmiş anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('category_id', 'idx_ctlg_cat_trans_cat_id');
            $table->index('language_code', 'idx_ctlg_cat_trans_lang_code');
            $table->unique(['category_id', 'language_code'], 'idx_ctlg_cat_trans_unique_lang');
        });

        Schema::create(self::BRAND_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün markalarının temel bilgilerini saklar.');

            $table->bigIncrements('brand_id')->comment('Marka için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Marka için benzersiz kod');
            $table->string('image', 255)->nullable()->comment('Marka logosunun URL veya dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Markaların listeleme sırası');
            $table->boolean('status')->default(false)->comment('Markanın durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_ctlg_brand_code');
            $table->index('status', 'idx_ctlg_brand_status');
            $table->index('sort_order', 'idx_ctlg_brand_sort_order');
        });

        Schema::create(self::BRAND_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Marka bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('brand_translation_id')->comment('Marka çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('brand_id')->comment('İlgili marka ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Markanın çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Markanın çevrilmiş kısa özeti');
            $table->text('description')->nullable()->comment('Markanın çevrilmiş detaylı açıklaması');
            $table->string('tag', 255)->nullable()->comment('Markanın çevrilmiş etiketleri');
            $table->string('keyword', 255)->nullable()->comment('Markanın SEO için çevrilmiş anahtar kelimeleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('brand_id', 'idx_ctlg_brand_trans_brand_id');
            $table->index('language_code', 'idx_ctlg_brand_trans_lang_code');
            $table->unique(['brand_id', 'language_code'], 'idx_ctlg_brand_trans_unique_lang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::BRAND_TRANSLATION_TABLE);
        Schema::dropIfExists(self::BRAND_TABLE);

        Schema::dropIfExists(self::CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CATEGORY_TABLE);

        Schema::dropIfExists(self::REVIEW_TABLE);
        Schema::dropIfExists(self::PRODUCT_ACTIVITY_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_VARIABLE_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_STOCK_TABLE);
        Schema::dropIfExists(self::PRODUCT_VARIANT_TABLE);
        Schema::dropIfExists(self::PRODUCT_OPTION_VALUE_TABLE);
        Schema::dropIfExists(self::PRODUCT_OPTION_TABLE);
        Schema::dropIfExists(self::PRODUCT_FILTER_VALUE_TABLE);
        Schema::dropIfExists(self::PRODUCT_FIELD_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_FIELD_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_ATTRIBUTE_TABLE);
        Schema::dropIfExists(self::PRODUCT_POST_TABLE);
        Schema::dropIfExists(self::PRODUCT_RELATED_TABLE);
        Schema::dropIfExists(self::PRODUCT_FAQ_TABLE);
        Schema::dropIfExists(self::PRODUCT_VIDEO_TABLE);
        Schema::dropIfExists(self::PRODUCT_IMAGE_TABLE);
        Schema::dropIfExists(self::PRODUCT_RECURRING_TABLE);
        Schema::dropIfExists(self::PRODUCT_BUNDLE_TABLE);
        Schema::dropIfExists(self::PRODUCT_GROUPED_TABLE);
        Schema::dropIfExists(self::PRODUCT_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PRODUCT_TABLE);
    }
};
