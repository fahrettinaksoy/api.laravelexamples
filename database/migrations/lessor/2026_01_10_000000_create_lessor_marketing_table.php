<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bu migration'ın kullanacağı veritabanı bağlantısının adı.
     * Bu özellik tanımlandığında, bu sınıf içindeki tüm Schema işlemleri
     * 'conn_lsr' bağlantısı üzerinde otomatik olarak yürütülür.
     */
    protected $connection = 'conn_lsr';

    // Kampanya Tabloları
    private const CAMPAIGN_TABLE = 'mrktng_campaign';
    private const CAMPAIGN_HISTORY_TABLE = 'mrktng_campaign_history';
    private const CAMPAIGN_TRANSLATION_TABLE = 'mrktng_campaign_translation';
    private const CAMPAIGN_PRODUCT_TABLE = 'mrktng_campaign_product';

    /**
     * Migration'ı çalıştır. Gerekli tüm pazarlama kampanya tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     *
     * @return void
     */
    public function up(): void
    {
        // mrktng_campaign tablosunu oluştur
        Schema::create(self::CAMPAIGN_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Pazarlama kampanyalarının temel bilgilerini saklar.');

            $table->bigIncrements('campaign_id')->comment('Kampanya için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Kampanya için benzersiz kod'); // 255 yerine 100
            $table->string('image', 255)->nullable()->comment('Kampanyanın ana görselinin URL veya dosya yolu');
            $table->unsignedBigInteger('campaign_type_id')->default(0)->comment('Kampanya tipi ID\'si (dfntn_mrktng_campaign_type.campaign_type_id)');
            $table->string('campaign_type_code', 50)->nullable()->comment('Kampanya tipinin kodu (dfntn_mrktng_campaign_type.code)'); // 255 yerine 50
            $table->text('campaign_type_setting')->nullable()->comment('Kampanya tipine özel ayarlar (JSON formatında)'); // text
            $table->unsignedBigInteger('layout_id')->default(0)->nullable()->comment('Kampanyanın kullanacağı sayfa düzeni ID\'si (varsa)');
            $table->boolean('status')->default(false)->comment('Kampanyanın durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_mrktng_camp_code');
            $table->index('campaign_type_id', 'idx_mrktng_camp_type_id');
            $table->index('status', 'idx_mrktng_camp_status');
        });

        // mrktng_campaign_history tablosunu oluştur
        Schema::create(self::CAMPAIGN_HISTORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazarlama kampanyalarının performans geçmişini (örn: dönüşümler) saklar.');

            $table->bigIncrements('campaign_history_id')->comment('Kampanya geçmişi kaydı için birincil anahtar');
            $table->unsignedBigInteger('campaign_id')->comment('İlgili kampanya ID\'si');
            $table->unsignedBigInteger('order_id')->default(0)->nullable()->comment('İlişkili sipariş ID\'si (eğer bir siparişten geliyorsa)');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('İlişkili hesap ID\'si (eğer bir kullanıcıdan geliyorsa)');
            $table->decimal('amount', 19, 4)->comment('Kampanya ile ilişkili miktar (örn: sipariş tutarı, harcanan miktar)'); // 19,2 yerine 19,4
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('campaign_id', 'idx_mrktng_camp_hist_camp_id');
            $table->index('order_id', 'idx_mrktng_camp_hist_order_id');
            $table->index('account_id', 'idx_mrktng_camp_hist_acc_id');
        });

        // mrktng_campaign_translation tablosunu oluştur
        Schema::create(self::CAMPAIGN_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazarlama kampanya bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('campaign_translation_id')->comment('Kampanya çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('campaign_id')->comment('İlgili kampanya ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('name', 255)->comment('Kampanyanın çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Kampanyanın çevrilmiş kısa özeti'); // 255 yerine 500
            $table->longText('description')->nullable()->comment('Kampanyanın çevrilmiş detaylı açıklaması');
            $table->longText('condition')->nullable()->comment('Kampanya koşullarının çevrilmiş metni (JSON veya metin)'); // text
            $table->string('keyword', 255)->nullable()->comment('SEO için çevrilmiş anahtar kelimeler');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması'); // 255 yerine 500
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('campaign_id', 'idx_mrktng_camp_trans_camp_id');
            $table->index('language_code', 'idx_mrktng_camp_trans_lang_code');
            $table->unique(['campaign_id', 'language_code'], 'idx_mrktng_camp_trans_unique_lang');
        });

        // mrktng_campaign_product tablosunu oluştur
        Schema::create(self::CAMPAIGN_PRODUCT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazarlama kampanyaları ile ürünler arasındaki ilişkiyi saklar.');

            $table->bigIncrements('campaign_product_id')->comment('Kampanya-ürün ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('campaign_id')->comment('İlgili kampanya ID\'si');
            $table->unsignedBigInteger('product_id')->comment('İlgili ürün ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('campaign_id', 'idx_mrktng_camp_prod_camp_id');
            $table->index('product_id', 'idx_mrktng_camp_prod_prod_id');
            $table->unique(['campaign_id', 'product_id'], 'idx_mrktng_camp_prod_unique');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_lsr' bağlantısı üzerinden siler.
     * Bu metot sadece bu migration'da oluşturulan tabloları silecektir.
     *
     * @return void
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::CAMPAIGN_PRODUCT_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_HISTORY_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_TABLE);
    }
};
