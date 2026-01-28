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

    // Kampanya Tipi Tanımlamaları
    private const CAMPAIGN_TYPE_TABLE = 'dfntn_mrktng_campaign_type';
    private const CAMPAIGN_TYPE_TRANSLATION_TABLE = 'dfntn_mrktng_campaign_type_translation';

    // Hediye Çeki Teması Tanımlamaları
    private const GIFT_VOUCHER_THEME_TABLE = 'dfntn_mrktng_gift_voucher_theme';
    private const GIFT_VOUCHER_THEME_TRANSLATION_TABLE = 'dfntn_mrktng_gift_voucher_theme_translation';

    /**
     * Migration'ı çalıştır. Gerekli tüm pazarlama tanımlama tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     *
     * @return void
     */
    public function up(): void
    {
        // dfntn_mrktng_campaign_type tablosunu oluştur
        Schema::create(self::CAMPAIGN_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Pazarlama kampanya tiplerini (örn: E-posta Kampanyası, SMS Kampanyası, Sosyal Medya Kampanyası) tanımlar.');

            $table->bigIncrements('campaign_type_id')->comment('Kampanya tipi için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Kampanya tipi için benzersiz kod'); // 255 yerine 100
            $table->string('image', 255)->nullable()->comment('Kampanya tipi ikon/görselinin URL veya dosya yolu');
            $table->boolean('status')->default(false)->comment('Kampanya tipinin durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_mrktng_camp_type_code');
            $table->index('status', 'idx_dfntn_mrktng_camp_type_status');
        });

        // dfntn_mrktng_campaign_type_translation tablosunu oluştur
        Schema::create(self::CAMPAIGN_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Pazarlama kampanya tipi tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('campaign_type_translation_id')->comment('Kampanya tipi çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('campaign_type_id')->comment('İlgili kampanya tipi ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('name', 255)->comment('Kampanya tipinin çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Kampanya tipinin çevrilmiş kısa özeti'); // 255 yerine 500
            $table->text('description')->nullable()->comment('Kampanya tipinin çevrilmiş detaylı açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('campaign_type_id', 'idx_dfntn_mrktng_camp_type_trans_id');
            $table->index('language_code', 'idx_dfntn_mrktng_camp_type_trans_lang_code');
            $table->unique(['campaign_type_id', 'language_code'], 'idx_dfntn_mrktng_camp_type_trans_unique_lang');
        });

        // dfntn_mrktng_gift_voucher_theme tablosunu oluştur
        Schema::create(self::GIFT_VOUCHER_THEME_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hediye çeki temalarını tanımlar.');

            $table->bigIncrements('gift_voucher_theme_id')->comment('Hediye çeki teması için birincil anahtar');
            $table->string('image', 255)->nullable()->comment('Tema görselinin URL veya dosya yolu');
            $table->boolean('status')->default(false)->comment('Temanın durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('status', 'idx_dfntn_mrktng_gift_vouch_theme_status');
        });

        // dfntn_mrktng_gift_voucher_theme_translation tablosunu oluştur
        Schema::create(self::GIFT_VOUCHER_THEME_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hediye çeki teması tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('gift_voucher_theme_translation_id')->comment('Hediye çeki teması çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('gift_voucher_theme_id')->comment('İlgili hediye çeki teması ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Temanın çevrilmiş adı');
            $table->text('description')->nullable()->comment('Temanın çevrilmiş açıklaması'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('gift_voucher_theme_id', 'idx_dfntn_mrktng_gift_vouch_theme_trans_id');
            $table->index('language_code', 'idx_dfntn_mrktng_gift_vouch_theme_trans_lang_code');
            $table->unique(['gift_voucher_theme_id', 'language_code'], 'idx_dfntn_mrktng_gift_vouch_theme_trans_unique_lang');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_lsr' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     *
     * @return void
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::GIFT_VOUCHER_THEME_TRANSLATION_TABLE);
        Schema::dropIfExists(self::GIFT_VOUCHER_THEME_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::CAMPAIGN_TYPE_TABLE);
    }
};
