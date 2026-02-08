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

    // Dil Tanımlamaları
    private const LANGUAGE_TABLE = 'dfntn_lclztn_language';

    // Para Birimi Tanımlamaları
    private const CURRENCY_TABLE = 'dfntn_lclztn_currency';

    // Ülke Tanımlamaları
    private const COUNTRY_TABLE = 'dfntn_lclztn_country';

    // Şehir Tanımlamaları
    private const CITY_TABLE = 'dfntn_lclztn_city';

    // İlçe Tanımlamaları
    private const DISTRICT_TABLE = 'dfntn_lclztn_district';

    // Coğrafi Bölge Tanımlamaları
    private const GEO_TABLE = 'dfntn_lclztn_geo';

    private const GEO_ZONE_TABLE = 'dfntn_lclztn_geo_zone';

    // Vergi Sınıfı Tanımlamaları
    private const TAX_CLASS_TABLE = 'dfntn_lclztn_tax_class';

    private const TAX_CLASS_TRANSLATION_TABLE = 'dfntn_lclztn_tax_class_translation';

    // Vergi Oranı Tanımlamaları
    private const TAX_RATE_TABLE = 'dfntn_lclztn_tax_rate';

    // Birim Tanımlamaları
    private const UNIT_TABLE = 'dfntn_lclztn_unit';

    private const UNIT_TRANSLATION_TABLE = 'dfntn_lclztn_unit_translation';

    // Adres Tipi Tanımlamaları
    private const ADDRESS_TYPE_TABLE = 'dfntn_lclztn_address_type';

    private const ADDRESS_TYPE_TRANSLATION_TABLE = 'dfntn_lclztn_address_type_translation';

    /**
     * Migration'ı çalıştır. Gerekli tüm yerelleştirme tanımlama tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     */
    public function up(): void
    {
        // dfntn_lclztn_language tablosunu oluştur
        Schema::create(self::LANGUAGE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Sistemde kullanılan dilleri tanımlar.');

            $table->bigIncrements('language_id')->comment('Dil için birincil anahtar');
            $table->string('name', 255)->comment('Dilin adı (örn: Türkçe, English)');
            $table->string('code', 10)->unique()->comment('Dilin ISO 639-1 kodu (örn: tr, en)'); // 255 yerine 10
            $table->string('locale', 50)->nullable()->comment('Dilin yerel ayar kodu (örn: tr_TR, en_US)'); // 255 yerine 50
            $table->string('image', 255)->nullable()->comment('Dil bayrağı görselinin URL veya dosya yolu');
            $table->string('directory', 50)->nullable()->comment('Dil dosyalarının bulunduğu dizin (varsa)');
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr')->comment('Yazı yönü (left-to-right, right-to-left)'); // 255 yerine enum
            $table->integer('sort_order')->default(0)->comment('Dillerin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Dilin durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_lcl_lang_code'); // Zaten unique, ek index belirtildi
            $table->index('status', 'idx_dfntn_lcl_lang_status');
            $table->index('sort_order', 'idx_dfntn_lcl_lang_sort_order');
        });

        // dfntn_lclztn_currency tablosunu oluştur
        Schema::create(self::CURRENCY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sistemde kullanılan para birimlerini tanımlar.');

            $table->bigIncrements('currency_id')->comment('Para birimi için birincil anahtar');
            $table->string('name', 255)->comment('Para biriminin adı (örn: Türk Lirası, Dolar)');
            $table->string('code', 3)->unique()->comment('Para biriminin ISO 4217 kodu (örn: TRY, USD)'); // 255 yerine 3
            $table->string('icon', 50)->nullable()->comment('Para birimi simgesi (örn: $, €)'); // 255 yerine 50
            $table->string('symbol_left', 10)->nullable()->comment('Sembolün sol tarafta gösterimi (örn: $)'); // 255 yerine 10
            $table->string('symbol_right', 10)->nullable()->comment('Sembolün sağ tarafta gösterimi (örn: TL)');
            $table->string('decimal_place', 2)->default('2')->comment('Ondalık basamak sayısı'); // 255 yerine 2
            $table->string('decimal_point', 1)->default(',')->comment('Ondalık ayıracı'); // 255 yerine 1
            $table->string('thousand_point', 1)->default('.')->comment('Binlik ayıracı'); // 255 yerine 1
            $table->decimal('value', 15, 8)->comment('Para biriminin varsayılan para birimine göre değeri (döviz kuru)'); // double yerine decimal
            $table->boolean('status')->default(false)->comment('Para biriminin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_lcl_curr_code'); // Zaten unique, ek index belirtildi
            $table->index('status', 'idx_dfntn_lcl_curr_status');
        });

        // dfntn_lclztn_country tablosunu oluştur
        Schema::create(self::COUNTRY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ülkeleri tanımlar.');

            $table->bigIncrements('country_id')->comment('Ülke için birincil anahtar');
            $table->string('name', 255)->comment('Ülkenin tam adı (örn: Türkiye Cumhuriyeti)');
            $table->string('title', 255)->comment('Ülkenin kısa adı (örn: Türkiye)');
            $table->string('iso_code_2', 2)->unique()->comment('ISO 3166-1 alpha-2 kodu (örn: TR)'); // 255 yerine 2
            $table->string('iso_code_3', 3)->unique()->comment('ISO 3166-1 alpha-3 kodu (örn: TUR)'); // 255 yerine 3
            $table->string('address_format', 500)->nullable()->comment('Adres formatı şablonu (JSON veya metin)'); // 255 yerine 500
            $table->boolean('postcode_required')->default(false)->comment('Posta kodu zorunlu mu?'); // tinyInteger yerine boolean
            $table->string('currency_code', 3)->nullable()->comment('Ülkenin varsayılan para birimi kodu');
            $table->string('language_code', 10)->nullable()->comment('Ülkenin varsayılan dil kodu');
            $table->string('time_zone', 100)->nullable()->comment('Ülkenin varsayılan zaman dilimi (örn: Europe/Istanbul)'); // 255 yerine 100
            $table->string('phone_code', 10)->nullable()->comment('Ülke telefon kodu (örn: +90)'); // 255 yerine 10
            $table->string('domain_extension', 10)->nullable()->comment('Ülke alan adı uzantısı (örn: .tr)'); // 255 yerine 10
            $table->string('date_format', 50)->nullable()->comment('Varsayılan tarih formatı (örn: Y-m-d)'); // 255 yerine 50
            $table->boolean('status')->default(false)->comment('Ülkenin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('iso_code_2', 'idx_dfntn_lcl_cntry_iso2'); // Zaten unique, ek index belirtildi
            $table->index('iso_code_3', 'idx_dfntn_lcl_cntry_iso3'); // Zaten unique, ek index belirtildi
            $table->index('status', 'idx_dfntn_lcl_cntry_status');
        });

        // dfntn_lclztn_city tablosunu oluştur
        Schema::create(self::CITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Şehirleri tanımlar.');

            $table->bigIncrements('city_id')->comment('Şehir için birincil anahtar');
            $table->unsignedBigInteger('country_id')->comment('İlgili ülke ID\'si');
            $table->string('name', 255)->comment('Şehrin adı');
            $table->string('iso_code_2', 10)->nullable()->comment('Şehir için ISO 2 kodu (varsa)'); // 255 yerine 10
            $table->string('iso_code_3', 10)->nullable()->comment('Şehir için ISO 3 kodu (varsa)');
            $table->string('traffic_code', 5)->nullable()->comment('Şehir trafik kodu (örn: 34 İstanbul)'); // 255 yerine 5
            $table->string('phone_code', 10)->nullable()->comment('Şehir telefon kodu (örn: 212 İstanbul Avrupa)');
            $table->text('svg_path')->nullable()->comment('Şehrin harita SVG yolu (varsa)');
            $table->boolean('status')->default(false)->comment('Şehrin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('country_id', 'idx_dfntn_lcl_city_cntry_id');
            $table->index('name', 'idx_dfntn_lcl_city_name');
            $table->index('status', 'idx_dfntn_lcl_city_status');
        });

        // dfntn_lclztn_district tablosunu oluştur
        Schema::create(self::DISTRICT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İlçeleri tanımlar.');

            $table->bigIncrements('district_id')->comment('İlçe için birincil anahtar');
            $table->unsignedBigInteger('country_id')->comment('İlgili ülke ID\'si');
            $table->unsignedBigInteger('city_id')->comment('İlgili şehir ID\'si');
            $table->string('name', 255)->comment('İlçenin adı');
            $table->string('post_code', 20)->nullable()->comment('İlçenin posta kodu'); // 255 yerine 20
            $table->boolean('status')->default(false)->comment('İlçenin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('country_id', 'idx_dfntn_lcl_dist_cntry_id');
            $table->index('city_id', 'idx_dfntn_lcl_dist_city_id');
            $table->index('name', 'idx_dfntn_lcl_dist_name');
            $table->index('status', 'idx_dfntn_lcl_dist_status');
        });

        // dfntn_lclztn_geo tablosunu oluştur
        Schema::create(self::GEO_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Coğrafi bölgeleri (örn: Türkiye, Avrupa, Asya) tanımlar.');

            $table->bigIncrements('geo_id')->comment('Coğrafi bölge için birincil anahtar');
            $table->string('name', 255)->comment('Coğrafi bölgenin adı');
            $table->string('description', 500)->nullable()->comment('Coğrafi bölgenin açıklaması');
            $table->boolean('status')->default(false)->comment('Coğrafi bölgenin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('name', 'idx_dfntn_lcl_geo_name');
            $table->index('status', 'idx_dfntn_lcl_geo_status');
        });

        // dfntn_lclztn_geo_zone tablosunu oluştur
        Schema::create(self::GEO_ZONE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Coğrafi bölgelerin içerdiği ülke, şehir ve ilçe kombinasyonlarını saklar.');

            $table->bigIncrements('geo_zone_id')->comment('Coğrafi bölge detayı için birincil anahtar');
            $table->unsignedBigInteger('geo_id')->comment('İlgili coğrafi bölge ID\'si');
            $table->unsignedBigInteger('country_id')->comment('İlgili ülke ID\'si');
            $table->unsignedBigInteger('city_id')->default(0)->nullable()->comment('İlgili şehir ID\'si (belirli bir şehir, 0 ise ülkenin tüm şehirleri)');
            $table->unsignedBigInteger('district_id')->default(0)->nullable()->comment('İlgili ilçe ID\'si (belirli bir ilçe, 0 ise şehrin tüm ilçeleri)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('geo_id', 'idx_dfntn_lcl_geo_zone_geo_id');
            $table->index('country_id', 'idx_dfntn_lcl_geo_zone_cntry_id');
            $table->index('city_id', 'idx_dfntn_lcl_geo_zone_city_id');
            $table->index('district_id', 'idx_dfntn_lcl_geo_zone_dist_id');
            $table->unique(['geo_id', 'country_id', 'city_id', 'district_id'], 'idx_dfntn_lcl_geo_zone_unique');
        });

        // dfntn_lclztn_tax_class tablosunu oluştur
        Schema::create(self::TAX_CLASS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergi sınıflarını (örn: KDV Genel, KDV İndirimli) tanımlar.');

            $table->bigIncrements('tax_class_id')->comment('Vergi sınıfı için birincil anahtar');
            $table->unsignedBigInteger('geo_id')->comment('İlgili coğrafi bölge ID\'si (vergi sınıfının uygulandığı bölge)');
            $table->string('type', 50)->comment('Vergi sınıfı tipi (örn: percentage, amount)'); // 255 yerine 50
            $table->decimal('rate', 15, 4)->comment('Vergi oranı (örn: 18.0000)');
            $table->integer('sort_order')->default(0)->comment('Vergi sınıflarının listeleme sırası');
            $table->boolean('status')->default(false)->comment('Vergi sınıfının durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('geo_id', 'idx_dfntn_lcl_tax_class_geo_id');
            $table->index('type', 'idx_dfntn_lcl_tax_class_type');
            $table->index('status', 'idx_dfntn_lcl_tax_class_status');
            $table->index('sort_order', 'idx_dfntn_lcl_tax_class_sort_order');
        });

        // dfntn_lclztn_tax_class_translation tablosunu oluştur
        Schema::create(self::TAX_CLASS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergi sınıfı tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('tax_class_translation_id')->comment('Vergi sınıfı çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('tax_class_id')->comment('İlgili vergi sınıfı ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Vergi sınıfının çevrilmiş adı');
            $table->string('short', 100)->nullable()->comment('Vergi sınıfının çevrilmiş kısa adı'); // 255 yerine 100
            $table->text('description')->nullable()->comment('Vergi sınıfının çevrilmiş açıklaması'); // 255 yerine text
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('tax_class_id', 'idx_dfntn_lcl_tax_class_trans_id');
            $table->index('language_code', 'idx_dfntn_lcl_tax_class_trans_lang_code');
            $table->unique(['tax_class_id', 'language_code'], 'idx_dfntn_lcl_tax_class_trans_unique_lang');
        });

        // dfntn_lclztn_tax_rate tablosunu oluştur
        Schema::create(self::TAX_RATE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Belirli coğrafi bölgelere uygulanan vergi oranlarını tanımlar.');

            $table->bigIncrements('tax_rate_id')->comment('Vergi oranı için birincil anahtar');
            $table->unsignedBigInteger('geo_id')->comment('İlgili coğrafi bölge ID\'si');
            $table->decimal('rate', 15, 4)->comment('Vergi oranı değeri (örn: 18.0000)');
            $table->string('name', 255)->comment('Vergi oranının adı (örn: KDV %18)');
            $table->string('type', 50)->comment('Vergi oranı tipi (örn: F, P - Fixed, Percentage)'); // 255 yerine 50
            $table->integer('sort_order')->default(0)->comment('Vergi oranlarının listeleme sırası');
            $table->boolean('status')->default(false)->comment('Vergi oranının durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('geo_id', 'idx_dfntn_lcl_tax_rate_geo_id');
            $table->index('name', 'idx_dfntn_lcl_tax_rate_name');
            $table->index('type', 'idx_dfntn_lcl_tax_rate_type');
            $table->index('status', 'idx_dfntn_lcl_tax_rate_status');
        });

        // dfntn_lclztn_unit tablosunu oluştur
        Schema::create(self::UNIT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ürün ve miktar birimlerini (örn: Adet, Kg, Metre) tanımlar.');

            $table->bigIncrements('unit_id')->comment('Birim için birincil anahtar');
            $table->decimal('value', 15, 8)->default(1.0)->comment('Birim değerini varsayılan birime göre'); // 15,8 hassasiyet
            $table->boolean('status')->default(false)->comment('Birimin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('status', 'idx_dfntn_lcl_unit_status');
        });

        // dfntn_lclztn_unit_translation tablosunu oluştur
        Schema::create(self::UNIT_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Birim tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('unit_translation_id')->comment('Birim çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('unit_id')->comment('İlgili birim ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Birimin çevrilmiş adı (örn: Kilogram)');
            $table->string('abbreviation', 50)->nullable()->comment('Birimin çevrilmiş kısaltması (örn: Kg)'); // 255 yerine 50
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('unit_id', 'idx_dfntn_lcl_unit_trans_unit_id');
            $table->index('language_code', 'idx_dfntn_lcl_unit_trans_lang_code');
            $table->unique(['unit_id', 'language_code'], 'idx_dfntn_lcl_unit_trans_unique_lang');
        });

        // dfntn_lclztn_address_type tablosunu oluştur
        Schema::create(self::ADDRESS_TYPE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Adres tiplerini (örn: Fatura Adresi, Teslimat Adresi, İş Adresi) tanımlar.');

            $table->bigIncrements('address_type_id')->comment('Adres tipi için birincil anahtar');
            $table->boolean('status')->default(false)->comment('Adres tipinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('status', 'idx_dfntn_lcl_addr_type_status');
        });

        // dfntn_lclztn_address_type_translation tablosunu oluştur
        Schema::create(self::ADDRESS_TYPE_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Adres tipi tanımlarının farklı dillere çevirilerini saklar.');

            $table->bigIncrements('address_type_translation_id')->comment('Adres tipi çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('address_type_id')->comment('İlgili adres tipi ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Adres tipinin çevrilmiş adı');
            $table->text('description')->nullable()->comment('Adres tipinin çevrilmiş açıklaması');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('address_type_id', 'idx_dfntn_lcl_addr_type_trans_id');
            $table->index('language_code', 'idx_dfntn_lcl_addr_type_trans_lang_code');
            $table->unique(['address_type_id', 'language_code'], 'idx_dfntn_lcl_addr_type_trans_unique_lang');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_lsr' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::ADDRESS_TYPE_TRANSLATION_TABLE);
        Schema::dropIfExists(self::ADDRESS_TYPE_TABLE);

        Schema::dropIfExists(self::UNIT_TRANSLATION_TABLE);
        Schema::dropIfExists(self::UNIT_TABLE);

        Schema::dropIfExists(self::TAX_RATE_TABLE);

        Schema::dropIfExists(self::TAX_CLASS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::TAX_CLASS_TABLE);

        Schema::dropIfExists(self::GEO_ZONE_TABLE);
        Schema::dropIfExists(self::GEO_TABLE);

        Schema::dropIfExists(self::DISTRICT_TABLE);
        Schema::dropIfExists(self::CITY_TABLE);
        Schema::dropIfExists(self::COUNTRY_TABLE);

        Schema::dropIfExists(self::CURRENCY_TABLE);
        Schema::dropIfExists(self::LANGUAGE_TABLE);
    }
};
