<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bu migration'ın kullanacağı veritabanı bağlantısının adı.
     * Bu özellik tanımlandığında, bu sınıf içindeki tüm Schema işlemleri
     * 'conn_desc' bağlantısı üzerinde otomatik olarak yürütülür.
     */
    protected $connection = 'conn_desc';

    // Uygulama Kategorisi Tabloları
    private const APP_CATEGORY_TABLE = 'dfntn_app_category';
    private const APP_CATEGORY_TRANSLATION_TABLE = 'dfntn_app_category_translation';

    // Uygulama Tabloları
    private const APP_APPLICATION_TABLE = 'dfntn_app_application';
    private const APP_APPLICATION_TRANSLATION_TABLE = 'dfntn_app_application_translation';
    private const APP_APPLICATION_IMAGE_TABLE = 'dfntn_app_application_image';
    private const APP_APPLICATION_FAQ_TABLE = 'dfntn_app_application_faq';
    private const APP_APPLICATION_POST_TABLE = 'dfntn_app_application_post';
    private const APP_APPLICATION_RELATED_TABLE = 'dfntn_app_application_related';
    private const APP_APPLICATION_CAMPAIGN_TABLE = 'dfntn_app_application_campaign';
    private const APP_APPLICATION_CHANNEL_SERVICES_TABLE = 'dfntn_app_application_channel_services';

    /**
     * Migration'ı çalıştır. Gerekli tüm uygulama tanımlama tablolarını belirtilen 'conn_desc' bağlantısı üzerinde oluşturur.
     *
     * @return void
     */
    public function up(): void
    {
        // dfntn_app_category tablosunu oluştur
        Schema::create(self::APP_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Uygulama kategorilerini (örn: CRM, ERP, E-ticaret) tanımlar.');

            $table->bigIncrements('category_id')->comment('Uygulama kategori için birincil anahtar');
            $table->unsignedBigInteger('parent_id')->default(0)->nullable()->comment('Üst kategoriye referans ID (0 ise ana kategori)');
            $table->string('type', 50)->nullable()->comment('Kategori tipi (örn: Yazılım, Hizmet)'); // 255 yerine 50
            $table->string('code', 100)->unique()->comment('Kategori için benzersiz kod'); // 255 yerine 100
            $table->string('image', 255)->nullable()->comment('Kategori görselinin URL veya dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Kategorilerin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Kategorinin durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('parent_id', 'idx_dfntn_app_cat_parent_id');
            $table->index('type', 'idx_dfntn_app_cat_type');
            $table->index('code', 'idx_dfntn_app_cat_code'); // Zaten unique, ek indeks belirtildi
            $table->index('status', 'idx_dfntn_app_cat_status');
            $table->index('sort_order', 'idx_dfntn_app_cat_sort_order');
        });

        // dfntn_app_category_translation tablosunu oluştur
        Schema::create(self::APP_CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulama kategori bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('category_translation_id')->comment('Kategori çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('category_id')->comment('İlgili uygulama kategori ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('name', 255)->comment('Kategorinin çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Kategorinin çevrilmiş kısa özeti'); // 255 yerine 500
            $table->text('description')->nullable()->comment('Kategorinin çevrilmiş detaylı açıklaması');
            $table->string('keyword', 255)->nullable()->comment('SEO için çevrilmiş anahtar kelimeler');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması'); // 255 yerine 500
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('category_id', 'idx_dfntn_app_cat_trans_cat_id');
            $table->index('language_code', 'idx_dfntn_app_cat_trans_lang_code');
            $table->unique(['category_id', 'language_code'], 'idx_dfntn_app_cat_trans_unique_lang');
        });

        // dfntn_app_application tablosunu oluştur
        Schema::create(self::APP_APPLICATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulamaların (App) temel bilgilerini saklar.');

            $table->bigIncrements('application_id')->comment('Uygulama için birincil anahtar');
            $table->unsignedBigInteger('category_id')->default(0)->comment('Uygulamanın ana kategori ID\'si');
            $table->unsignedBigInteger('developer_id')->default(0)->nullable()->comment('Uygulama geliştiricisinin ID\'si (varsa)');
            $table->string('type', 50)->nullable()->comment('Uygulama tipi (örn: Web, Mobil, Masaüstü)');
            $table->string('code', 100)->unique()->comment('Uygulama için benzersiz kod');
            $table->string('icon', 255)->nullable()->comment('Uygulama ikonunun URL veya dosya yolu');
            $table->string('cover', 255)->nullable()->comment('Uygulama kapak görselinin URL veya dosya yolu');
            $table->string('banner', 255)->nullable()->comment('Uygulama banner görselinin URL veya dosya yolu');
            $table->string('video', 255)->nullable()->comment('Uygulama tanıtım videosunun URL veya dosya yolu');

            $table->integer('license')->default(0)->comment('Lisans tipi (örn: 0: Ücretsiz, 1: Ücretli, 2: Abonelik)');
            $table->decimal('price', 19, 4)->comment('Uygulamanın fiyatı'); // 19,2 yerine 19,4
            $table->string('currency_code', 3)->comment('Fiyatın para birimi kodu'); // 255 yerine 3
            $table->unsignedBigInteger('tax_class_id')->default(0)->nullable()->comment('Uygulanacak vergi sınıfı ID\'si');
            $table->string('version', 50)->nullable()->comment('Uygulama versiyonu'); // 255 yerine 50

            $table->unsignedTinyInteger('rating')->default(0)->comment('Uygulamanın ortalama puanı (örn: 0-5 arası)'); // integer yerine unsignedTinyInteger
            $table->unsignedInteger('download')->default(0)->comment('Uygulamanın indirilme sayısı'); // integer yerine unsignedInteger
            $table->string('support_mail', 255)->nullable()->comment('Destek e-posta adresi');
            $table->string('support_website', 255)->nullable()->comment('Destek web sitesi URL\'si');
            $table->string('support_document', 255)->nullable()->comment('Destek dokümanı URL veya dosya yolu');
            $table->boolean('status')->default(false)->comment('Uygulamanın durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->dateTime('date_lastupdate')->nullable()->comment('Uygulamanın son güncelleme tarihi ve saati'); // timestamp(0) yerine dateTime, nullable
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('category_id', 'idx_dfntn_app_app_cat_id');
            $table->index('developer_id', 'idx_dfntn_app_app_dev_id');
            $table->index('type', 'idx_dfntn_app_app_type');
            $table->index('code', 'idx_dfntn_app_app_code'); // Zaten unique, ek indeks belirtildi
            $table->index('status', 'idx_dfntn_app_app_status');
        });

        // dfntn_app_application_translation tablosunu oluştur
        Schema::create(self::APP_APPLICATION_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulama bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('application_translation_id')->comment('Uygulama çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('application_id')->comment('İlgili uygulama ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Uygulamanın çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Uygulamanın çevrilmiş kısa özeti');
            $table->string('tag', 255)->nullable()->comment('Uygulamanın çevrilmiş etiketleri (virgülle ayrılmış)');
            $table->string('keyword', 255)->nullable()->comment('Uygulamanın SEO için çevrilmiş anahtar kelimeleri');
            $table->text('description')->nullable()->comment('Uygulamanın çevrilmiş detaylı açıklaması');
            $table->text('requirement')->nullable()->comment('Uygulamanın çevrilmiş gereksinimleri');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('application_id', 'idx_dfntn_app_app_trans_app_id');
            $table->index('language_code', 'idx_dfntn_app_app_trans_lang_code');
            $table->unique(['application_id', 'language_code'], 'idx_dfntn_app_app_trans_unique_lang');
        });

        // dfntn_app_application_image tablosunu oluştur
        Schema::create(self::APP_APPLICATION_IMAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulamalara ait ek görselleri (ekran görüntüleri vb.) saklar.');

            $table->bigIncrements('application_image_id')->comment('Uygulama görseli için birincil anahtar');
            $table->unsignedBigInteger('application_id')->comment('İlgili uygulama ID\'si');
            $table->string('file', 255)->comment('Görsel dosyasının URL veya dosya yolu');
            $table->string('description', 500)->nullable()->comment('Görsel açıklaması'); // 255 yerine 500
            $table->integer('sort_order')->default(0)->comment('Görsellerin gösterim sırası');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('application_id', 'idx_dfntn_app_app_img_app_id');
            $table->index('sort_order', 'idx_dfntn_app_app_img_sort_order');
        });

        // dfntn_app_application_faq tablosunu oluştur
        Schema::create(self::APP_APPLICATION_FAQ_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulamalar ile Sıkça Sorulan Sorular (FAQ) arasındaki ilişkiyi saklar.');

            $table->bigIncrements('application_faq_id')->comment('Uygulama-FAQ ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('application_id')->comment('İlgili uygulama ID\'si');
            $table->unsignedBigInteger('faq_id')->comment('İlgili FAQ ID\'si (spprt_faq.faq_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('application_id', 'idx_dfntn_app_app_faq_app_id');
            $table->index('faq_id', 'idx_dfntn_app_app_faq_faq_id');
            $table->unique(['application_id', 'faq_id'], 'idx_dfntn_app_app_faq_unique');
        });

        // dfntn_app_application_post tablosunu oluştur
        Schema::create(self::APP_APPLICATION_POST_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulamalar ile blog gönderileri arasındaki ilişkiyi saklar.');

            $table->bigIncrements('application_post_id')->comment('Uygulama-Blog gönderisi ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('application_id')->comment('İlgili uygulama ID\'si');
            $table->unsignedBigInteger('post_id')->comment('İlgili blog gönderisi ID\'si (site_blog_post.post_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('application_id', 'idx_dfntn_app_app_post_app_id');
            $table->index('post_id', 'idx_dfntn_app_app_post_post_id');
            $table->unique(['application_id', 'post_id'], 'idx_dfntn_app_app_post_unique');
        });

        // dfntn_app_application_related tablosunu oluştur
        Schema::create(self::APP_APPLICATION_RELATED_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulamalar arası ilişkiyi (ilgili uygulamalar) saklar.');

            $table->bigIncrements('application_related_id')->comment('İlgili uygulama ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('application_id')->comment('Ana uygulama ID\'si');
            $table->unsignedBigInteger('related_id')->comment('İlgili uygulama ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('application_id', 'idx_dfntn_app_app_rel_app_id');
            $table->index('related_id', 'idx_dfntn_app_app_rel_related_id');
            $table->unique(['application_id', 'related_id'], 'idx_dfntn_app_app_rel_unique');
        });

        // dfntn_app_application_campaign tablosunu oluştur
        Schema::create(self::APP_APPLICATION_CAMPAIGN_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulamalar ile pazarlama kampanyaları arasındaki ilişkiyi saklar.');

            // Renamed from application_related_id to application_campaign_id for clarity
            $table->bigIncrements('application_campaign_id')->comment('Uygulama-Kampanya ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('application_id')->comment('İlgili uygulama ID\'si');
            $table->unsignedBigInteger('campaign_id')->comment('İlgili kampanya ID\'si (mrktng_campaign.campaign_id)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('application_id', 'idx_dfntn_app_app_camp_app_id');
            $table->index('campaign_id', 'idx_dfntn_app_app_camp_camp_id');
            $table->unique(['application_id', 'campaign_id'], 'idx_dfntn_app_app_camp_unique');
        });

        // dfntn_app_application_channel_services tablosunu oluştur
        Schema::create(self::APP_APPLICATION_CHANNEL_SERVICES_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Uygulamaların kullandığı kanal servislerini (örn: ödeme ağ geçitleri, SMS servisleri) saklar.');

            $table->bigIncrements('application_channel_service_id')->comment('Uygulama-kanal servisi ilişki kaydı için birincil anahtar');
            $table->string('application_code', 100)->comment('İlgili uygulamanın kodu (dfntn_app_application.code)'); // 255 yerine 100
            $table->string('channel_service_code', 100)->comment('İlgili kanal servisinin kodu'); // 255 yerine 100
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->unique(['application_code', 'channel_service_code'], 'idx_dfntn_app_app_chan_serv_unique');
            $table->index('application_code', 'idx_dfntn_app_app_chan_serv_app_code');
            $table->index('channel_service_code', 'idx_dfntn_app_app_chan_serv_chan_code');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_desc' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     *
     * @return void
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::APP_APPLICATION_CHANNEL_SERVICES_TABLE);
        Schema::dropIfExists(self::APP_APPLICATION_CAMPAIGN_TABLE);
        Schema::dropIfExists(self::APP_APPLICATION_RELATED_TABLE);
        Schema::dropIfExists(self::APP_APPLICATION_POST_TABLE);
        Schema::dropIfExists(self::APP_APPLICATION_FAQ_TABLE);
        Schema::dropIfExists(self::APP_APPLICATION_IMAGE_TABLE);
        Schema::dropIfExists(self::APP_APPLICATION_TRANSLATION_TABLE);
        Schema::dropIfExists(self::APP_APPLICATION_TABLE);
        Schema::dropIfExists(self::APP_CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::APP_CATEGORY_TABLE);
    }
};
