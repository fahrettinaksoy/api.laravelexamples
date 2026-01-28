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

    // Genel Sistem Ayarları Tablosu
    private const SETTING_TABLE = 'systm_setting';

    // Şirket Bazlı Tanımlamalar/Ayarlar Tablosu
    private const COMPANY_TABLE = 'systm_company';

    // Sistem Metotları (Ödeme, Kargo vb. Metotların Yapılandırması) Tablosu
    private const METHOD_TABLE = 'systm_method';

    // Kullanıcı Grubu Tablosu
    private const USER_GROUP_TABLE = 'systm_user_group';

    // Kullanıcı Tablosu
    private const USER_TABLE = 'systm_user';

    // Kullanıcı Şifre Sıfırlama Tokenı Tablosu
    private const USER_TOKEN_RESET_TABLE = 'systm_user_token_reset';

    // Kullanıcı API Erişim Tokenı Tablosu (Sanctum benzeri)
    private const USER_TOKEN_ACCESS_TABLE = 'systm_user_token_access';

    // Sistem Widgetları (Görsel Bileşenler) Tablosu
    private const WIDGET_TABLE = 'systm_widget';

    /**
     * Migration'ı çalıştır. Gerekli tüm sistem tablolarını belirtilen 'conn_lsr' bağlantısı üzerinde oluşturur.
     *
     * @return void
     */
    public function up(): void
    {
        // systm_setting tablosunu oluştur
        Schema::create(self::SETTING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; // Geniş karakter desteği için utf8mb4 önerilir
            $table->collation = 'utf8mb4_unicode_ci'; // Doğru sıralama ve arama için utf8mb4_unicode_ci önerilir
            $table->comment('Genel sistem ayarlarının ve yapılandırmalarının saklandığı tablo.');

            $table->bigIncrements('setting_id')->comment('Sistem ayarı için birincil anahtar');
            $table->string('option', 100)->unique()->comment('Ayar anahtarı (örn: "site_name", "contact_email")'); // 255 yerine 100, unique
            $table->string('slug', 100)->nullable()->comment('Ayara ait URL dostu kimlik veya alternatif kod'); // 255 yerine 100
            $table->longText('value')->nullable()->comment('Ayarın depolandığı değer (JSON, seri hale getirilmiş dizi vb.)'); // text yerine longText
            $table->boolean('serialized')->default(false)->comment('Değerin seri hale getirilmiş olup olmadığı (0: Hayır, 1: Evet)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('option', 'idx_systm_setting_option');
            $table->index('slug', 'idx_systm_setting_slug');
            $table->index('serialized', 'idx_systm_setting_serialized');
        });

        // systm_company tablosunu oluştur
        Schema::create(self::COMPANY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Şirket bazında genel tanımlamaların veya ayarların saklandığı tablo.');

            $table->bigIncrements('definition_id')->comment('Şirket tanımı/ayarı için birincil anahtar');
            $table->string('option', 100)->unique()->comment('Tanım anahtarı (örn: "company_tax_id", "company_address")'); // 255 yerine 100, unique
            $table->string('slug', 100)->nullable()->comment('Tanıma ait URL dostu kimlik veya alternatif kod'); // 255 yerine 100
            $table->longText('value')->nullable()->comment('Tanımın depolandığı değer'); // text yerine longText
            $table->boolean('view')->default(false)->comment('Tanımın genel görünürlük durumu (0: Görünmez, 1: Görünür)'); // tinyInteger yerine boolean
            $table->boolean('serialized')->default(false)->comment('Değerin seri hale getirilmiş olup olmadığı'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('option', 'idx_systm_company_option');
            $table->index('slug', 'idx_systm_company_slug');
            $table->index('view', 'idx_systm_company_view');
            $table->index('serialized', 'idx_systm_company_serialized');
        });

        // systm_method tablosunu oluştur
        Schema::create(self::METHOD_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sistemde tanımlı metotların (örn: ödeme metotları, kargo metotları) yapılandırma bilgilerini saklar.');

            $table->bigIncrements('method_id')->comment('Metot için birincil anahtar');
            $table->string('method_group', 50)->comment('İlgili metot grubunun kodu (örn: "payment", "shipping")'); // 255 yerine 50
            $table->string('method_type', 50)->comment('İlgili metot tipinin kodu (örn: "credit_card", "cash_on_delivery")'); // 255 yerine 50
            $table->longText('setting')->nullable()->comment('Metodun yapılandırma ayarları (JSON formatında)'); // text yerine longText
            $table->integer('sort_order')->default(0)->comment('Metotların listeleme sırası');
            $table->boolean('status')->default(false)->comment('Metodun durumu (0: Pasif, 1: Aktif)'); // tinyInteger yerine boolean
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->unique(['method_group', 'method_type'], 'idx_systm_method_unique_type_group'); // Metot grubu içinde metot tipi benzersiz olmalı
            $table->index('method_group', 'idx_systm_method_group');
            $table->index('method_type', 'idx_systm_method_type');
            $table->index('status', 'idx_systm_method_status');
            $table->index('sort_order', 'idx_systm_method_sort_order');
        });

        // systm_user_group tablosunu oluştur
        Schema::create(self::USER_GROUP_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kullanıcı gruplarını ve yetkilerini saklar.');

            $table->bigIncrements('user_group_id')->comment('Kullanıcı grubu için birincil anahtar');
            $table->string('code', 50)->unique()->comment('Kullanıcı grubu için benzersiz kod (örn: "admin", "editor")'); // 255 yerine 50
            $table->string('name', 255)->comment('Kullanıcı grubunun adı');
            $table->text('description')->nullable()->comment('Kullanıcı grubunun açıklaması'); // 255 yerine text
            $table->longText('permission')->nullable()->comment('Kullanıcı grubunun sahip olduğu izinler (JSON formatında)'); // text yerine longText
            $table->boolean('status')->default(false)->comment('Kullanıcı grubunun durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_systm_user_group_code');
            $table->index('status', 'idx_systm_user_group_status');
        });

        // systm_user tablosunu oluştur
        Schema::create(self::USER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sistem kullanıcılarını saklar (Admin Paneli kullanıcıları).');

            $table->bigIncrements('user_id')->comment('Kullanıcı için birincil anahtar');
            $table->string('code', 50)->unique()->nullable()->comment('Kullanıcı için benzersiz kod/kullanıcı adı'); // 255 yerine 50
            $table->string('firstname', 100)->comment('Kullanıcının adı'); // 255 yerine 100
            $table->string('lastname', 100)->comment('Kullanıcının soyadı');
            $table->string('image', 255)->nullable()->comment('Kullanıcı profil görseli URL/dosya yolu');
            $table->string('email', 255)->unique()->comment('E-posta adresi (benzersiz, login için kullanılır)');
            $table->timestamp('email_verified_at')->nullable()->comment('E-posta doğrulama zamanı');
            $table->unsignedBigInteger('user_group_id')->default(0)->comment('Kullanıcının ait olduğu kullanıcı grubu ID\'si');
            $table->string('password', 255)->comment('Şifre (hashlenmiş)'); // Laravel'in varsayılan şifre uzunluğu
            $table->rememberToken()->comment('Beni Hatırla tokenı');
            $table->string('ip_address', 45)->nullable()->comment('Son login veya oluşturma IP adresi (IPv6 desteği için 45 karakter)'); // string yerine 45
            $table->boolean('status')->default(false)->comment('Kullanıcının durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_systm_user_code');
            $table->index('email', 'idx_systm_user_email'); // Zaten unique, ek index belirtildi
            $table->index('user_group_id', 'idx_systm_user_group_id');
            $table->index('status', 'idx_systm_user_status');
        });

        // systm_user_token_reset tablosunu oluştur (Laravel'in password_resets'e benzer custom implementasyon)
        Schema::create(self::USER_TOKEN_RESET_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kullanıcılar için şifre sıfırlama tokenlarını saklar.');

            $table->bigIncrements('user_token_reset_id')->comment('Kullanıcı token sıfırlama kaydı için birincil anahtar');
            $table->string('email', 255)->index()->comment('Şifresi sıfırlanacak kullanıcının e-postası');
            $table->string('token', 64)->unique()->comment('Şifre sıfırlama için benzersiz token'); // 255 yerine 64
            $table->timestamp('created_at')->nullable()->comment('Tokenın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı'); // Eklenen updated_at

            $table->index('email', 'idx_systm_user_token_reset_email');
            $table->index('token', 'idx_systm_user_token_reset_token'); // Zaten unique, ek index belirtildi
        });

        // systm_user_token_access tablosunu oluştur (Laravel Sanctum Personal Access Token yapısına benzer)
        Schema::create(self::USER_TOKEN_ACCESS_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kullanıcılar için API veya özel erişim tokenlarını saklar (Sanctum benzeri).');

            $table->bigIncrements('user_token_access_id')->comment('Token erişim kaydı için birincil anahtar');
            $table->morphs('tokenable')->comment('Tokenın ait olduğu model (örn: user, personnel)'); // tokenable_type ve tokenable_id sütunları oluşturur
            $table->string('name', 255)->comment('Tokenın adı/tanımı');
            $table->string('token', 64)->unique()->comment('Benzersiz token değeri (hashed veya raw)');
            $table->longText('abilities')->nullable()->comment('Tokenın sahip olduğu yetenekler/izinler (JSON formatında)'); // text yerine longText
            $table->timestamp('last_used_at')->nullable()->comment('Tokenın en son kullanıldığı zaman');
            $table->timestamp('expires_at')->nullable()->comment('Tokenın geçerlilik süresinin bitiş zamanı');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index(['tokenable_type', 'tokenable_id'], 'idx_systm_user_token_access_morph'); // morflar için standart indeks
            $table->index('name', 'idx_systm_user_token_access_name');
            $table->index('token', 'idx_systm_user_token_access_token'); // Zaten unique, ek index belirtildi
        });

        // systm_widget tablosunu oluştur
        Schema::create(self::WIDGET_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sistemde tanımlı widgetların (görsel bileşenler) yapılandırma bilgilerini saklar.');

            $table->bigIncrements('widget_id')->comment('Widget için birincil anahtar');
            $table->string('widget_type', 100)->comment('Widget tipinin kodu (örn: "html", "javascript")'); // 255 yerine 100
            $table->string('name', 255)->comment('Widgetın adı');
            $table->text('css')->nullable()->comment('Widgeta özel CSS kodları'); // string yerine text
            $table->longText('html')->nullable()->comment('Widgetın özel HTML içeriği veya şablon yolu'); // string yerine longText
            $table->longText('setting')->nullable()->comment('Widgetın yapılandırma ayarları (JSON formatında)'); // text yerine longText
            $table->boolean('status')->default(false)->comment('Widgetın durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('widget_type', 'idx_systm_widget_type');
            $table->index('status', 'idx_systm_widget_status');
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
        Schema::dropIfExists(self::USER_TOKEN_ACCESS_TABLE);
        Schema::dropIfExists(self::USER_TOKEN_RESET_TABLE);
        Schema::dropIfExists(self::USER_TABLE);
        Schema::dropIfExists(self::USER_GROUP_TABLE);
        Schema::dropIfExists(self::WIDGET_TABLE); // Missing from original down method, added here.
        Schema::dropIfExists(self::METHOD_TABLE);
        Schema::dropIfExists(self::COMPANY_TABLE);
        Schema::dropIfExists(self::SETTING_TABLE);
    }
};
