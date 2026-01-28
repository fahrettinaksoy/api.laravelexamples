<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_lsr';

    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_ACCOUNT = 'wbst_account';
    private const TABLE_ACCOUNT_ACCESS = 'wbst_account_access';
    private const TABLE_ACCOUNT_ACCESS_TRANSLATION = 'wbst_account_access_translation';
    private const TABLE_ACCOUNT_AUTHORIZED = 'wbst_account_authorized';
    private const TABLE_ACCOUNT_AUTHORIZED_TOKEN_RESET = 'wbst_account_authorized_token_reset';
    private const TABLE_ACCOUNT_TOKEN_ACCESS = 'wbst_account_token_access';
    private const TABLE_ACCOUNT_CONTACT = 'wbst_account_contact';
    private const TABLE_ACCOUNT_BANK_ACCOUNT = 'wbst_account_bank_account';
    private const TABLE_ACCOUNT_SMS_VERIFY = 'wbst_account_sms_verify';

    public function up(): void
    {
        Schema::create(self::TABLE_ACCOUNT, function (Blueprint $table) {
            $table->comment('Sistemdeki ana hesap bilgilerini (müşteri, satıcı, iş ortağı) tutar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_id')->comment('Hesap benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Hesap tipi ID (Bireysel, Kurumsal vb.)');
            $table->unsignedBigInteger('group_id')->default(0)->comment('Hesap grubu ID (VIP, Bayi vb.)');
            
            $table->string('code', 100)->comment('Hesap için benzersiz kod veya kullanıcı adı');
            $table->tinyInteger('shape')->default(0)->comment('Hesap şekli (0: Gerçek Kişi, 1: Tüzel Kişi)');
            $table->string('language_code', 10)->nullable()->comment('Hesabın tercih ettiği dil kodu (tr, en)');
            $table->string('image', 500)->nullable()->comment('Hesap profili görseli veya logo');
            $table->string('name', 255)->index()->comment('Hesap adı veya Ticari Unvan');
            $table->string('tax_office', 255)->nullable()->comment('Vergi Dairesi');
            $table->string('tax_number', 50)->nullable()->comment('Vergi Kimlik Numarası');
            $table->string('trade_chamber', 255)->nullable()->comment('Ticaret Odası');
            $table->string('trade_number', 50)->nullable()->comment('Ticaret Sicil Numarası');
            $table->string('mersis_number', 50)->nullable()->comment('Mersis Numarası');
            $table->string('kep_address', 255)->nullable()->comment('KEP (Kayıtlı Elektronik Posta) Adresi');
            $table->text('component')->nullable()->comment('Hesaba özel ek bileşen ayarları (JSON)');
            $table->boolean('safe')->default(false)->comment('Güvenli mod aktif mi?');
            $table->boolean('newsletter')->default(false)->comment('Bülten aboneliği var mı?');
            $table->unsignedBigInteger('default_authorized_id')->nullable()->default(0)->comment('Varsayılan yetkili ID');
            $table->unsignedBigInteger('default_contact_id')->nullable()->default(0)->comment('Varsayılan iletişim adresi ID');
            $table->unsignedBigInteger('default_bank_account_id')->nullable()->default(0)->comment('Varsayılan banka hesabı ID');
            $table->string('ip_address', 50)->nullable()->comment('Kayıt sırasındaki IP adresi');
            $table->boolean('status')->default(true)->comment('Hesap durumu (Aktif/Pasif)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');
            
            $table->unique('code', 'idx_acc_code_unique');
            $table->index('type_id', 'idx_acc_type');
            $table->index('group_id', 'idx_acc_group');
            $table->index('status', 'idx_acc_status');
            $table->index('tax_number', 'idx_acc_tax_num');
            $table->index('mersis_number', 'idx_acc_mersis');
            $table->index('created_at', 'idx_acc_created_at');
            
            $table->index(['type_id', 'status', 'deleted_at'], 'idx_acc_type_status_del');
            $table->index(['group_id', 'status', 'deleted_at'], 'idx_acc_group_status_del');
            $table->index(['deleted_at', 'status'], 'idx_acc_del_status');
        });

        Schema::create(self::TABLE_ACCOUNT_ACCESS, function (Blueprint $table) {
            $table->comment('Hesaba özel sistem yapılandırması ve erişim ayarları (Multi-tenancy).');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('access_id')->comment('Erişim ayarı kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Bağlı olduğu hesap ID');
            $table->unsignedBigInteger('sector_id')->default(0)->nullable()->comment('Sektör bilgisi ID');
            $table->string('code', 100)->nullable()->comment('Özel erişim kodu');
            $table->string('debug', 20)->default('false')->comment('Hata ayıklama modu (true/false)');
            $table->string('log_channel', 50)->default('stack')->comment('Log kanalı');
            $table->string('log_deprecation', 20)->default('false')->comment('Eski kod uyarılarını loglama');
            $table->string('log_level', 20)->default('debug')->comment('Log seviyesi (debug/info/error)');
            $table->string('session_driver', 50)->default('file')->comment('Oturum sürücüsü (file/redis/database)');
            $table->string('session_lifetime', 10)->default('120')->comment('Oturum süresi (dakika)');
            $table->string('broadcast_driver', 50)->default('log')->comment('Yayın sürücüsü');
            $table->string('cache_driver', 50)->default('file')->comment('Önbellek sürücüsü');
            $table->string('queue_connection', 50)->default('sync')->comment('Kuyruk bağlantı türü');
            $table->string('domain_protocol', 10)->default('https')->comment('Web protokolü (http/https)');
            $table->string('domain_address', 255)->comment('Ana alan adı (domain)');
            $table->string('domain_extension', 10)->nullable()->comment('Alan adı uzantısı (.com, .net vb.)');
            $table->unsignedBigInteger('domain_id')->default(0)->nullable()->comment('Alan adı sistem ID');
            $table->string('domain_guid', 100)->nullable()->comment('Alan adı benzersiz ID');
            $table->boolean('subdomain_status')->default(false)->comment('Alt alan adı kullanımı aktif mi?');
            $table->string('subdomain_name', 100)->nullable()->comment('Alt alan adı (subdomain)');
            $table->boolean('host_shelter')->default(false)->comment('Hosting sistem içinde mi barınıyor?');
            $table->string('host_address', 255)->nullable()->comment('Host IP adresi');
            $table->string('host_port', 10)->nullable()->comment('Host port numarası');
            $table->string('host_username', 100)->nullable()->comment('Host kullanıcı adı');
            $table->string('host_password', 255)->nullable()->comment('Host şifresi');
            $table->boolean('database_shelter')->default(false)->comment('Veritabanı sistem içinde mi barınıyor?');
            $table->string('database_connection', 50)->nullable()->comment('Veritabanı bağlantı türü (mysql, pgsql)');
            $table->unsignedBigInteger('database_id')->default(0)->nullable()->comment('Veritabanı sistem ID');
            $table->string('database_host', 255)->nullable()->comment('Veritabanı sunucu adresi');
            $table->string('database_port', 10)->nullable()->comment('Veritabanı port numarası');
            $table->string('database_name', 100)->nullable()->comment('Veritabanı adı');
            $table->string('database_username', 100)->nullable()->comment('Veritabanı kullanıcı adı');
            $table->unsignedBigInteger('database_username_id')->default(0)->nullable()->comment('Kullanıcı sistem ID');
            $table->string('database_password', 255)->nullable()->comment('Veritabanı şifresi');
            $table->boolean('storage_shelter')->default(false)->comment('Depolama sistem içinde mi barınıyor?');
            $table->string('storage_engine', 50)->nullable()->comment('Depolama motoru (local, s3, ftp)');
            $table->unsignedBigInteger('storage_id')->default(0)->nullable()->comment('Depolama sistem ID');
            $table->string('storage_host', 255)->nullable()->comment('Depolama sunucusu');
            $table->string('storage_username', 100)->nullable()->comment('Depolama kullanıcı adı');
            $table->string('storage_password', 255)->nullable()->comment('Depolama şifresi');
            $table->string('storage_url', 500)->nullable()->comment('Depolama genel URL');
            $table->string('storage_root', 255)->nullable()->comment('Depolama kök dizin');
            $table->string('storage_folder', 255)->nullable()->comment('Depolama klasörü');
            $table->string('storage_path', 255)->nullable()->comment('Depolama tam yolu');
            $table->string('storage_timeout', 10)->nullable()->comment('Zaman aşımı süresi');
            $table->string('language_default', 10)->nullable()->comment('Varsayılan dil');
            $table->text('language_usable')->nullable()->comment('Kullanılabilir diller (JSON)');
            $table->string('currency_default', 3)->nullable()->comment('Varsayılan para birimi');
            $table->text('currency_usable')->nullable()->comment('Kullanılabilir para birimleri (JSON)');
            $table->unsignedBigInteger('theme_id')->default(0)->nullable()->comment('Seçili tema ID');
            $table->unsignedBigInteger('partner_id')->default(0)->nullable()->comment('İş ortağı ID');
            $table->boolean('reference')->default(false)->comment('Referans hesap mı?');
            $table->integer('sort_order')->default(0)->comment('Sıralama değeri');
            $table->boolean('status')->default(false)->comment('Erişim durumu');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');
            
            $table->unique('code', 'idx_access_code_unique');
            $table->index('account_id', 'idx_access_account');
            $table->index('sector_id', 'idx_access_sector');
            $table->index('domain_address', 'idx_access_domain');
            $table->index('subdomain_name', 'idx_access_subdomain');
            
            $table->index(['status', 'deleted_at'], 'idx_access_status_active');
            $table->index(['domain_address', 'status', 'deleted_at'], 'idx_access_domain_active');
            $table->index(['account_id', 'status', 'deleted_at'], 'idx_access_account_status');
        });

        Schema::create(self::TABLE_ACCOUNT_ACCESS_TRANSLATION, function (Blueprint $table) {
            $table->comment('Hesap erişim ayarlarının çoklu dil çevirileri.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_access_translation_id')->comment('Çeviri benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('access_id')->comment('Bağlı erişim kaydı ID');
            $table->char('language_code', 5)->comment('Dil kodu');
            $table->string('name', 255)->comment('Görünen ad');
            $table->string('summary', 500)->nullable()->comment('Kısa özet');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->string('tag', 255)->nullable()->comment('Etiketler');
            $table->string('keyword', 255)->nullable()->comment('Anahtar kelimeler');
            $table->text('about')->nullable()->comment('Hakkında yazısı');
            $table->text('issue')->nullable()->comment('Sorun bildirimi metni');
            $table->text('solution')->nullable()->comment('Çözüm önerisi metni');
            $table->text('comment')->nullable()->comment('Ek notlar');
            $table->string('meta_title', 255)->nullable()->comment('SEO Başlık');
            $table->string('meta_description', 500)->nullable()->comment('SEO Açıklama');
            $table->string('meta_keyword', 255)->nullable()->comment('SEO Anahtarlar');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');
            
            $table->index('access_id', 'idx_access_trans_access_id');
            $table->index('language_code', 'idx_access_trans_lang');
            $table->unique(['access_id', 'language_code'], 'idx_access_trans_unique_lang');
            
            $table->index('name', 'idx_access_trans_name');
        });

        Schema::create(self::TABLE_ACCOUNT_AUTHORIZED, function (Blueprint $table) {
            $table->comment('Hesap yetkilileri ve sisteme giriş yapabilen kullanıcı bilgilerini tutar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_authorized_id')->comment('Yetkili benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Bağlı hesap ID');
            $table->unsignedBigInteger('access_id')->default(0)->comment('Özel erişim ID');
            $table->unsignedBigInteger('authorized_group_id')->default(0)->comment('Yetki grubu ID');
            $table->string('firstname', 100)->comment('Ad');
            $table->string('lastname', 100)->comment('Soyad');
            $table->string('citizenship_number', 20)->nullable()->comment('TCKN / Pasaport No');
            $table->tinyInteger('gender')->default(0)->comment('Cinsiyet (0:Belirsiz, 1:Erkek, 2:Kadın)');
            $table->date('birth_date')->nullable()->comment('Doğum Tarihi');
            $table->string('title', 100)->nullable()->comment('Unvan / Pozisyon');
            $table->string('description', 500)->nullable()->comment('Açıklama');
            $table->string('phone_constant', 50)->nullable()->comment('Sabit Telefon');
            $table->string('phone_mobile', 50)->nullable()->comment('Cep Telefonu');
            $table->string('email', 255)->comment('E-posta adresi (Benzersiz, Kullanıcı Adı)');
            $table->timestamp('sms_verified_at')->nullable()->comment('SMS doğrulama zamanı');
            $table->timestamp('email_verified_at')->nullable()->comment('E-posta doğrulama zamanı');
            $table->string('email_verified_token', 64)->nullable()->comment('E-posta doğrulama tokenı');
            $table->string('password', 255)->comment('Şifre (Hash)');
            $table->rememberToken()->comment('Beni hatırla tokenı');
            $table->boolean('status')->default(true)->comment('Durum (Aktif/Pasif)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');
            
            $table->unique('email', 'idx_auth_email_unique');
            $table->unique('phone_mobile', 'idx_auth_phone_unique');
            $table->unique('citizenship_number', 'idx_auth_citizen_unique');
            
            $table->index('account_id', 'idx_auth_account');
            $table->index('authorized_group_id', 'idx_auth_group');
            $table->index('status', 'idx_auth_status');
            
            $table->index(['email', 'status', 'deleted_at'], 'idx_auth_login');
            $table->index(['account_id', 'status', 'deleted_at'], 'idx_auth_acc_active');
        });

        Schema::create(self::TABLE_ACCOUNT_AUTHORIZED_TOKEN_RESET, function (Blueprint $table) {
            $table->comment('Yetkili şifre sıfırlama işlemlerinde kullanılan tokenları tutar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('token_reset_id')->comment('Sıfırlama işlemi ID');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('email', 255)->index()->comment('Sıfırlama talep edilen e-posta');
            $table->string('token', 100)->unique()->comment('Sıfırlama anahtarı');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Token oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Token son güncellenme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi'); 

            $table->index('created_at', 'idx_token_reset_created');
        });

        Schema::create(self::TABLE_ACCOUNT_TOKEN_ACCESS, function (Blueprint $table) {
            $table->comment('API erişim tokenlarını ve özel entegrasyon anahtarlarını saklar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('token_access_id')->comment('Token benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->morphs('tokenable');
            $table->string('name', 255)->comment('Token adı / Tanımı');
            $table->string('token', 100)->comment('Erişim Tokenı');
            $table->text('abilities')->nullable()->comment('Yetkiler (JSON)');
            $table->timestamp('last_used_at')->nullable()->comment('Son kullanım zamanı');
            $table->timestamp('expires_at')->nullable()->comment('Geçerlilik bitiş zamanı');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');
            
            $table->unique('token', 'idx_api_token_unique');
            $table->index('name', 'idx_api_token_name');
            $table->index(['tokenable_type', 'tokenable_id', 'deleted_at'], 'idx_api_token_model');
            $table->index(['token', 'expires_at'], 'idx_api_token_validity');
        });

        Schema::create(self::TABLE_ACCOUNT_CONTACT, function (Blueprint $table) {
            $table->comment('Hesaplara ait tüm iletişim ve adres detaylarını saklar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_contact_id')->comment('İletişim kaydı ID');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Bağlı hesap ID');
            $table->unsignedBigInteger('taxpayer_type_id')->default(0)->nullable()->comment('Mükellef tipi');
            $table->unsignedBigInteger('address_type_id')->default(0)->nullable()->comment('Adres tipi (Fatura, Teslimat vb.)');
            $table->boolean('abroad')->default(false)->comment('Yurtdışı adresi mi?');
            $table->string('title', 100)->nullable()->comment('Adres başlığı (Ev, İş)');
            $table->string('firstname', 100)->nullable()->comment('İlgili kişi ad');
            $table->string('lastname', 100)->nullable()->comment('İlgili kişi soyad');
            $table->string('citizenship_number', 20)->nullable()->comment('İlgili kişi TCKN');
            $table->string('company', 255)->nullable()->comment('İlgili şirket adı');
            $table->string('tax_office', 255)->nullable()->comment('Vergi dairesi');
            $table->string('tax_number', 50)->nullable()->comment('Vergi numarası');
            $table->string('email', 255)->nullable()->comment('İletişim e-posta');
            $table->string('telephone', 50)->nullable()->comment('İletişim telefon');
            $table->string('address_1', 255)->comment('Adres satırı 1');
            $table->string('address_2', 255)->nullable()->comment('Adres satırı 2');
            $table->unsignedBigInteger('country_id')->default(0)->index()->comment('Ülke ID');
            $table->unsignedBigInteger('city_id')->default(0)->index()->comment('Şehir ID');
            $table->unsignedBigInteger('district_id')->default(0)->nullable()->comment('İlçe ID');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->boolean('status')->default(true)->comment('Durum (Aktif/Pasif)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');
            
            $table->index('account_id', 'idx_contact_account');
            $table->index('address_type_id', 'idx_contact_type');
            $table->index('status', 'idx_contact_status');
            $table->index('tax_number', 'idx_contact_tax');
            $table->index(['account_id', 'status', 'deleted_at'], 'idx_contact_acc_active');
            $table->index(['account_id', 'address_type_id', 'status', 'deleted_at'], 'idx_contact_acc_type_active');
        });

        Schema::create(self::TABLE_ACCOUNT_BANK_ACCOUNT, function (Blueprint $table) {
            $table->comment('Hesaplara ait banka hesap bilgilerini (IBAN vb.) tutar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_bank_account_id')->comment('Banka hesabı kayıt ID');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Bağlı hesap ID');
            $table->unsignedBigInteger('bank_id')->default(0)->comment('Banka tanım ID');
            $table->string('currency_code', 3)->nullable()->comment('Para birimi (TRY, USD)');
            $table->string('type', 50)->nullable()->comment('Hesap türü (Vadesiz vb.)');
            $table->string('name', 255)->comment('Hesap adı');
            $table->string('owner', 255)->comment('Hesap sahibi');
            $table->string('account_number', 50)->nullable()->comment('Hesap numarası');
            $table->string('branch_code', 20)->nullable()->comment('Şube kodu');
            $table->string('iban', 50)->comment('IBAN Numarası');
            
            $table->boolean('status')->default(true)->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');
            
            $table->unique('iban', 'idx_bank_iban_unique');
            $table->index('account_id', 'idx_bank_account');
            $table->index('bank_id', 'idx_bank_bank_id');
            $table->index('status', 'idx_bank_status');
            $table->index(['account_id', 'status', 'deleted_at'], 'idx_bank_acc_active');
        });

        Schema::create(self::TABLE_ACCOUNT_SMS_VERIFY, function (Blueprint $table) {
            $table->comment('Yetkili kullanıcılar için gönderilen SMS doğrulama kodlarını tutar.');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_sms_verify_id')->comment('SMS doğrulama kayıt ID');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_authorized_id')->comment('İlgili yetkili ID');
            $table->string('code', 10)->comment('Doğrulama kodu');
            $table->timestamp('expires_at')->nullable()->comment('Kodun geçerlilik süresi');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');
            
            $table->index('account_authorized_id', 'idx_sms_auth_id');
            $table->index(['account_authorized_id', 'code', 'expires_at'], 'idx_sms_verify_check');
            $table->index('expires_at', 'idx_sms_expires');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_ACCOUNT_SMS_VERIFY);
        Schema::dropIfExists(self::TABLE_ACCOUNT_BANK_ACCOUNT);
        Schema::dropIfExists(self::TABLE_ACCOUNT_CONTACT);
        Schema::dropIfExists(self::TABLE_ACCOUNT_TOKEN_ACCESS);
        Schema::dropIfExists(self::TABLE_ACCOUNT_AUTHORIZED_TOKEN_RESET);
        Schema::dropIfExists(self::TABLE_ACCOUNT_AUTHORIZED);
        Schema::dropIfExists(self::TABLE_ACCOUNT_ACCESS_TRANSLATION);
        Schema::dropIfExists(self::TABLE_ACCOUNT_ACCESS);
        Schema::dropIfExists(self::TABLE_ACCOUNT);
    }
};
