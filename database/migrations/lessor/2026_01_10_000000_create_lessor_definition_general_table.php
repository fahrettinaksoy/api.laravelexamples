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

    // Şube Tanımlamaları
    private const BRANCH_TABLE = 'dfntn_gnrl_branch';

    // İş Ortağı Kategori Tanımlamaları
    private const PARTNER_CATEGORY_TABLE = 'prtnr_category';

    private const PARTNER_CATEGORY_TRANSLATION_TABLE = 'prtnr_category_translation';

    // İşletme (İş Ortağı) Tanımlamaları
    private const PARTNER_BUSINESS_TABLE = 'prtnr_business';

    private const PARTNER_BUSINESS_TRANSLATION_TABLE = 'prtnr_business_translation';

    private const PARTNER_BUSINESS_CATEGORY_TABLE = 'prtnr_business_category';

    /**
     * Migration'ı çalıştır. Gerekli tüm iş ortağı ve şube tanımlama tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     */
    public function up(): void
    {
        // dfntn_gnrl_branch tablosunu oluştur
        Schema::create(self::BRANCH_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Şirket şubeleri veya mağaza konumlarının temel bilgilerini saklar.');

            $table->bigIncrements('branch_id')->comment('Şube için birincil anahtar');
            $table->string('code', 50)->unique()->comment('Şube için benzersiz kod'); // 255 yerine 50
            $table->string('type', 50)->nullable()->comment('Şube tipi (örn: Merkez, Mağaza, Depo)');
            $table->string('name', 255)->comment('Şube adı');
            $table->string('company', 255)->nullable()->comment('Şubenin ilişkili olduğu şirket adı');
            $table->string('authorized', 255)->nullable()->comment('Şube yetkilisi/sorumlusu');
            $table->string('image', 255)->nullable()->comment('Şube görselinin URL veya dosya yolu');
            $table->string('website', 255)->nullable()->comment('Şubenin web sitesi');
            $table->string('email', 255)->nullable()->comment('Şubenin e-posta adresi');
            $table->string('phone_number', 50)->nullable()->comment('Şubenin sabit telefon numarası'); // 255 yerine 50
            $table->string('fax_number', 50)->nullable()->comment('Şubenin faks numarası');
            $table->string('gsm_number', 50)->nullable()->comment('Şubenin mobil telefon numarası');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Şubenin bulunduğu ülke ID\'si'); // string yerine unsignedBigInteger
            $table->unsignedBigInteger('city_id')->nullable()->comment('Şubenin bulunduğu şehir ID\'si'); // string yerine unsignedBigInteger
            $table->unsignedBigInteger('district_id')->nullable()->comment('Şubenin bulunduğu ilçe ID\'si'); // string yerine unsignedBigInteger
            $table->string('postcode', 20)->nullable()->comment('Şubenin posta kodu'); // 255 yerine 20
            $table->string('address_1', 255)->comment('Şubenin adres satırı 1');
            $table->string('address_2', 255)->nullable()->comment('Şubenin adres satırı 2');
            $table->string('map_coordinate', 255)->nullable()->comment('Şubenin harita koordinatları (örn: latitude,longitude)');
            $table->boolean('status')->default(false)->comment('Şubenin durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_dfntn_gnrl_branch_code');
            $table->index('type', 'idx_dfntn_gnrl_branch_type');
            $table->index('status', 'idx_dfntn_gnrl_branch_status');
            $table->index('country_id', 'idx_dfntn_gnrl_branch_country_id');
            $table->index('city_id', 'idx_dfntn_gnrl_branch_city_id');
        });

        // prtnr_category tablosunu oluştur
        Schema::create(self::PARTNER_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İş ortaklarının kategorilerini (örn: Tedarikçi, Servis Sağlayıcı, Distribütör) tanımlar.');

            $table->bigIncrements('category_id')->comment('İş ortağı kategori için birincil anahtar');
            $table->unsignedBigInteger('parent_id')->default(0)->nullable()->comment('Üst kategoriye referans ID (0 ise ana kategori)');
            $table->unsignedBigInteger('type_id')->default(0)->nullable()->comment('Kategori tipi ID\'si (örn: Hukuki, Operasyonel)');
            $table->string('code', 100)->unique()->comment('Kategori için benzersiz kod');
            $table->string('image', 255)->nullable()->comment('Kategori görselinin URL veya dosya yolu');
            $table->integer('sort_order')->default(0)->comment('Kategorilerin listeleme sırası');
            $table->boolean('status')->default(false)->comment('Kategorinin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('date_modified')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('parent_id', 'idx_prtnr_cat_parent_id');
            $table->index('type_id', 'idx_prtnr_cat_type_id');
            $table->index('code', 'idx_prtnr_cat_code');
            $table->index('status', 'idx_prtnr_cat_status');
            $table->index('sort_order', 'idx_prtnr_cat_sort_order');
        });

        // prtnr_category_translation tablosunu oluştur
        Schema::create(self::PARTNER_CATEGORY_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İş ortağı kategori bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('category_translation_id')->comment('Kategori çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('category_id')->comment('İlgili iş ortağı kategori ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('Kategorinin çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('Kategorinin çevrilmiş kısa özeti');
            $table->text('description')->nullable()->comment('Kategorinin çevrilmiş detaylı açıklaması');
            $table->string('keyword', 255)->nullable()->comment('SEO için çevrilmiş anahtar kelimeler');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('category_id', 'idx_prtnr_cat_trans_cat_id');
            $table->index('language_code', 'idx_prtnr_cat_trans_lang_code');
            $table->unique(['category_id', 'language_code'], 'idx_prtnr_cat_trans_unique_lang');
        });

        // prtnr_business tablosunu oluştur
        Schema::create(self::PARTNER_BUSINESS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İşletmelerin (İş Ortakları) temel bilgilerini saklar.');

            $table->bigIncrements('business_id')->comment('İşletme için birincil anahtar');
            $table->string('code', 100)->unique()->comment('İşletme için benzersiz kod');
            $table->string('type', 50)->nullable()->comment('İşletme tipi (örn: Tedarikçi, Müşteri, Distribütör)');
            $table->string('name', 255)->comment('İşletme adı');
            $table->string('company', 255)->nullable()->comment('İşletmenin bağlı olduğu şirket adı (varsa)');
            $table->string('authorized', 255)->nullable()->comment('İşletme yetkilisi/irtibat kişisi');
            $table->string('image', 255)->nullable()->comment('İşletme logosunun URL veya dosya yolu');
            $table->string('website', 255)->nullable()->comment('İşletmenin web sitesi');
            $table->string('email', 255)->nullable()->comment('İşletmenin ana e-posta adresi');
            $table->string('phone_number', 50)->nullable()->comment('İşletmenin sabit telefon numarası');
            $table->string('fax_number', 50)->nullable()->comment('İşletmenin faks numarası');
            $table->string('gsm_number', 50)->nullable()->comment('İşletmenin mobil telefon numarası');
            $table->unsignedBigInteger('country_id')->nullable()->comment('İşletmenin bulunduğu ülke ID\'si');
            $table->unsignedBigInteger('city_id')->nullable()->comment('İşletmenin bulunduğu şehir ID\'si');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İşletmenin bulunduğu ilçe ID\'si');
            $table->string('postcode', 20)->nullable()->comment('İşletmenin posta kodu');
            $table->string('address_1', 255)->comment('İşletmenin adres satırı 1');
            $table->string('address_2', 255)->nullable()->comment('İşletmenin adres satırı 2');
            $table->string('map_coordinate', 255)->nullable()->comment('İşletmenin harita koordinatları');
            $table->integer('sort_order')->default(0)->comment('İşletmelerin listeleme sırası');
            $table->boolean('status')->default(false)->comment('İşletmenin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_prtnr_bus_code');
            $table->index('type', 'idx_prtnr_bus_type');
            $table->index('status', 'idx_prtnr_bus_status');
            $table->index('country_id', 'idx_prtnr_bus_country_id');
            $table->index('city_id', 'idx_prtnr_bus_city_id');
        });

        // prtnr_business_translation tablosunu oluştur
        Schema::create(self::PARTNER_BUSINESS_TRANSLATION_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İşletme (iş ortağı) bilgilerinin farklı dillere çevirilerini saklar.');

            $table->bigIncrements('business_translation_id')->comment('İşletme çeviri kaydı için birincil anahtar');
            $table->unsignedBigInteger('business_id')->comment('İlgili işletme ID\'si');
            $table->string('language_code', 10)->comment('Çevirinin yapıldığı dil kodu');
            $table->string('name', 255)->comment('İşletmenin çevrilmiş adı');
            $table->string('summary', 500)->nullable()->comment('İşletmenin çevrilmiş kısa özeti');
            $table->text('description')->nullable()->comment('İşletmenin çevrilmiş detaylı açıklaması');
            $table->text('about')->nullable()->comment('İşletme hakkında çevrilmiş bilgi');
            $table->text('advantage')->nullable()->comment('İşletmenin avantajları hakkında çevrilmiş bilgi');
            $table->text('application')->nullable()->comment('İşletmenin uygulama/kullanım alanları hakkında çevrilmiş bilgi');
            $table->string('keyword', 255)->nullable()->comment('SEO için çevrilmiş anahtar kelimeler');
            $table->string('meta_title', 255)->nullable()->comment('SEO için çevrilmiş meta başlığı');
            $table->string('meta_description', 500)->nullable()->comment('SEO için çevrilmiş meta açıklaması');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO için çevrilmiş meta anahtar kelimeler');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('business_id', 'idx_prtnr_bus_trans_bus_id');
            $table->index('language_code', 'idx_prtnr_bus_trans_lang_code');
            $table->unique(['business_id', 'language_code'], 'idx_prtnr_bus_trans_unique_lang');
        });

        // prtnr_business_category tablosunu oluştur
        Schema::create(self::PARTNER_BUSINESS_CATEGORY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('İşletmeler ile iş ortağı kategorileri arasındaki ilişkiyi saklar.');

            $table->bigIncrements('business_category_id')->comment('İşletme-kategori ilişki kaydı için birincil anahtar');
            $table->unsignedBigInteger('business_id')->comment('İlgili işletme ID\'si');
            $table->unsignedBigInteger('category_id')->comment('İlgili iş ortağı kategori ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('business_id', 'idx_prtnr_bus_cat_bus_id');
            $table->index('category_id', 'idx_prtnr_bus_cat_cat_id');
            $table->unique(['business_id', 'category_id'], 'idx_prtnr_bus_cat_unique');
        });
    }

    /**
     * Migration'ı geri al. Tabloları belirtilen 'conn_lsr' bağlantısı üzerinden siler.
     * Bağımlılık sırasına dikkat edilerek silinme işlemi yapılır.
     */
    public function down(): void
    {
        // Bağımlılık sırasına dikkat ederek tabloları sil
        Schema::dropIfExists(self::PARTNER_BUSINESS_CATEGORY_TABLE);
        Schema::dropIfExists(self::PARTNER_BUSINESS_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PARTNER_BUSINESS_TABLE);

        Schema::dropIfExists(self::PARTNER_CATEGORY_TRANSLATION_TABLE);
        Schema::dropIfExists(self::PARTNER_CATEGORY_TABLE);

        Schema::dropIfExists(self::BRANCH_TABLE);
    }
};
