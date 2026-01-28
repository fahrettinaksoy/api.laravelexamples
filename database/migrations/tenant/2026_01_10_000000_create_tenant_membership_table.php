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

    private const TABLE_ACCOUNT = 'mem_account';
    private const TABLE_ACCOUNT_AUTHORIZED = 'mem_account_authorized';
    private const TABLE_ACCOUNT_CONTACT = 'mem_account_contact';
    private const TABLE_ACCOUNT_BANK = 'mem_account_bank_account';
    private const TABLE_ACCOUNT_DOWNLOAD = 'mem_account_download';
    private const TABLE_ACCOUNT_REWARD = 'mem_account_reward';
    private const TABLE_ACCOUNT_WISHLIST = 'mem_account_wishlist';
    private const TABLE_ACCOUNT_WISHLIST_ITEM = 'mem_account_wishlist_item';
    private const TABLE_ACCOUNT_PRICE_ALERT = 'mem_account_price_alert';
    private const TABLE_ACCOUNT_PREORDER = 'mem_account_preorder';
    private const TABLE_ACCOUNT_PASSWORD_RESET = 'mem_account_password_reset';
    private const TABLE_ACCOUNT_VERIFICATION = 'mem_account_verification';
    private const TABLE_ACCOUNT_ACCESS_TOKEN = 'mem_account_access_token';

    public function up(): void
    {
        Schema::create(self::TABLE_ACCOUNT, function (Blueprint $table) {
            $table->comment('Müşteri/Hesap ana tablosu (Bireysel ve Kurumsal)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_id')->comment('Hesap benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 50)->unique()->nullable()->comment('Müşteri kodu/Cari kodu');
            $table->unsignedBigInteger('parent_id')->default(0)->index()->comment('Üst hesap kimliği (Bayi/Alt bayi ilişkisi)');
            $table->unsignedInteger('account_group_id')->default(1)->index()->comment('Hesap grubu kimliği');
            $table->unsignedInteger('account_type_id')->default(1)->comment('Hesap türü (1: Bireysel, 2: Kurumsal)');
            $table->string('email', 255)->unique()->comment('E-posta adresi (Kullanıcı adı)');
            $table->string('phone', 20)->nullable()->comment('Telefon numarası');
            $table->string('password', 255)->comment('Şifre (Hash)');
            $table->boolean('is_foreign')->default(false)->comment('Yabancı uyruklu/Yurt dışı hesabı mı?');
            $table->string('name', 255)->comment('Ad Soyad veya Şirket Ünvanı');
            $table->string('short_name', 100)->nullable()->comment('Kısa ad veya ticari marka');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi');
            $table->string('tax_number', 50)->nullable()->index()->comment('Vergi numarası veya TC Kimlik No');
            $table->string('mersis_number', 50)->nullable()->comment('Mersis numarası');
            $table->string('trade_chamber', 100)->nullable()->comment('Ticaret odası');
            $table->string('trade_number', 50)->nullable()->comment('Ticaret sicil numarası');
            $table->string('kep_address', 255)->nullable()->comment('KEP adresi');
            $table->string('logo', 500)->nullable()->comment('Logo/Avatar URL');
            $table->decimal('monthly_budget_limit', 19, 4)->default(0)->comment('Aylık harcama limiti');
            $table->decimal('annual_budget_limit', 19, 4)->default(0)->comment('Yıllık harcama limiti');
            $table->char('currency_code', 3)->default('TRY')->comment('Varsayılan para birimi');
            $table->char('language_code', 5)->default('tr')->comment('Varsayılan dil');
            $table->string('timezone', 50)->default('Europe/Istanbul')->comment('Saat dilimi');
            $table->boolean('newsletter_allowed')->default(false)->comment('Bülten aboneliği izni');
            $table->timestamp('email_verified_at')->nullable()->comment('E-posta doğrulama zamanı');
            $table->timestamp('phone_verified_at')->nullable()->comment('Telefon doğrulama zamanı');
            $table->timestamp('last_login_at')->nullable()->comment('Son giriş zamanı');
            $table->ipAddress('last_login_ip')->nullable()->comment('Son giriş IP adresi');
            $table->rememberToken();
            $table->unsignedBigInteger('country_id')->nullable()->comment('Ülke kimliği');
            $table->unsignedBigInteger('city_id')->nullable()->comment('Şehir kimliği');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe kimliği');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->string('address', 500)->nullable()->comment('Açık adres');
            $table->string('website', 255)->nullable()->comment('Web sitesi');
            $table->text('notes')->nullable()->comment('Yönetici notları');
            $table->boolean('is_verified')->default(false)->comment('Hesap onaylı mı?');
            $table->boolean('status')->default(false)->index()->comment('Durum (Aktif/Pasif/Banlı)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['created_by', 'created_at'], 'idx_account_audit');
            $table->index('deleted_at', 'idx_account_soft_delete');
        });

        Schema::create(self::TABLE_ACCOUNT_PASSWORD_RESET, function (Blueprint $table) {
            $table->comment('Şifre sıfırlama talepleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->string('email', 255)->index();
            $table->string('token', 255);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create(self::TABLE_ACCOUNT_AUTHORIZED, function (Blueprint $table) {
            $table->comment('Kurumsal hesap yetkilileri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_authorized_id')->comment('Yetkili benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->string('first_name', 100)->comment('Ad');
            $table->string('last_name', 100)->comment('Soyad');
            $table->string('title', 100)->nullable()->comment('Ünvan (CEO, Müdür vb.)');
            $table->string('department', 100)->nullable()->comment('Departman');
            $table->string('phone', 20)->nullable()->comment('Dahili telefon');
            $table->string('mobile', 20)->nullable()->comment('Cep telefonu');
            $table->string('email', 100)->unique()->comment('E-posta adresi');
            $table->string('identity_number', 20)->nullable()->comment('TC Kimlik No');
            $table->date('birth_date')->nullable()->comment('Doğum tarihi');
            $table->unsignedInteger('gender_id')->default(0)->comment('Cinsiyet kimliği');
            $table->boolean('is_primary')->default(false)->comment('Ana yetkili mi?');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('account_id', 'idx_authorized_account');
            $table->index('deleted_at', 'idx_authorized_soft_delete');
        });

        Schema::create(self::TABLE_ACCOUNT_CONTACT, function (Blueprint $table) {
            $table->comment('Hesap adres ve iletişim bilgileri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_contact_id')->comment('İletişim benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->string('title', 100)->nullable()->comment('Adres başlığı (Ev, İş vb.)');
            $table->string('first_name', 100)->comment('Ad');
            $table->string('last_name', 100)->comment('Soyad');
            $table->string('company', 255)->nullable()->comment('Firma adı');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi');
            $table->string('tax_number', 50)->nullable()->comment('Vergi numarası');
            $table->string('phone', 20)->nullable()->comment('Telefon');
            $table->string('email', 255)->nullable()->comment('E-posta');
            
            $table->unsignedBigInteger('country_id')->comment('Ülke');
            $table->unsignedBigInteger('city_id')->comment('Şehir');
            $table->unsignedBigInteger('district_id')->comment('İlçe');
            $table->string('neighborhood', 100)->nullable()->comment('Mahalle');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->string('address_1', 255)->comment('Adres satırı 1');
            $table->string('address_2', 255)->nullable()->comment('Adres satırı 2');
            
            $table->boolean('is_default_billing')->default(false)->comment('Varsayılan fatura adresi');
            $table->boolean('is_default_shipping')->default(false)->comment('Varsayılan teslimat adresi');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('account_id', 'idx_contact_account');
            $table->index('is_default_billing', 'idx_contact_def_bill');
            $table->index('is_default_shipping', 'idx_contact_def_ship');
            $table->index('deleted_at', 'idx_contact_soft_delete');
        });

        Schema::create(self::TABLE_ACCOUNT_BANK, function (Blueprint $table) {
            $table->comment('Müşteri banka hesapları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_bank_account_id')->comment('Banka hesap benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->unsignedBigInteger('bank_id')->comment('Banka tanım kimliği');
            $table->string('branch_name', 100)->nullable()->comment('Şube adı');
            $table->string('branch_code', 50)->nullable()->comment('Şube kodu');
            $table->string('account_number', 50)->nullable()->comment('Hesap numarası');
            $table->string('iban', 50)->unique()->comment('IBAN numarası');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi');
            $table->boolean('is_default')->default(false)->comment('Varsayılan banka hesabı mı?');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('account_id', 'idx_account_bank_account');
            $table->index('deleted_at', 'idx_account_bank_soft_delete');
        });

        Schema::create(self::TABLE_ACCOUNT_DOWNLOAD, function (Blueprint $table) {
            $table->comment('Müşteriye tanımlı indirilebilir ürünler tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_download_id')->comment('Kayıt benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->unsignedBigInteger('order_id')->comment('Sipariş kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedBigInteger('download_id')->comment('İndirme dosyası kimliği');
            $table->unsignedInteger('remaining_count')->default(0)->comment('Kalan indirme hakkı');
            $table->timestamp('expired_at')->nullable()->comment('Erişim bitiş tarihi');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['account_id', 'product_id'], 'idx_acc_download_lookup');
            $table->index('deleted_at', 'idx_acc_download_soft_delete');
        });

        Schema::create(self::TABLE_ACCOUNT_REWARD, function (Blueprint $table) {
            $table->comment('Müşteri ödül puanı geçmişi tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('account_reward_id')->comment('Kayıt benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->unsignedBigInteger('order_id')->default(0)->comment('İlişkili sipariş kimliği (varsa)');
            $table->string('description', 255)->comment('Puan hareketi açıklaması');
            $table->integer('points')->default(0)->comment('Puan miktarı (+/-)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            
            $table->index('account_id', 'idx_reward_account');
        });

        Schema::create(self::TABLE_ACCOUNT_WISHLIST, function (Blueprint $table) {
            $table->comment('Alışveriş/Favori listeleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('wishlist_id')->comment('Liste benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->string('name', 255)->comment('Liste adı');
            $table->string('token', 100)->unique()->nullable()->comment('Paylaşım tokeni');
            $table->boolean('is_public')->default(false)->comment('Herkese açık mı?');
            
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['account_id', 'deleted_at'], 'idx_wishlist_account_active');
        });

        Schema::create(self::TABLE_ACCOUNT_WISHLIST_ITEM, function (Blueprint $table) {
            $table->comment('Alışveriş listesi ürünleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('wishlist_item_id')->comment('Liste elemanı benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('wishlist_id')->comment('Liste kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('quantity')->default(1)->comment('Hedeflenen adet');
            $table->integer('priority')->default(0)->comment('Öncelik sırası');
            
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            
            $table->unique(['wishlist_id', 'product_id'], 'idx_wishlist_item_unique');
        });

        Schema::create(self::TABLE_ACCOUNT_PRICE_ALERT, function (Blueprint $table) {
            $table->comment('Fiyat düşüş alarmı tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('price_alert_id')->comment('Alarm benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->decimal('target_price', 19, 4)->nullable()->comment('Hedeflenen fiyat (Altına düşerse bildir)');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('notified_at')->nullable()->comment('Bildirim gönderilme zamanı');
            
            $table->index(['product_id', 'target_price'], 'idx_price_alert_check');
        });

        Schema::create(self::TABLE_ACCOUNT_PREORDER, function (Blueprint $table) {
            $table->comment('Ön sipariş talepleri tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('preorder_id')->comment('Ön sipariş benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('quantity')->default(1)->comment('Talep edilen adet');
            $table->boolean('is_notified')->default(false)->comment('Stok gelince bildirildi mi?');
            
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            
            $table->index(['product_id', 'is_notified'], 'idx_preorder_product_notification');
        });

        Schema::create(self::TABLE_ACCOUNT_VERIFICATION, function (Blueprint $table) {
            $table->comment('Hesap doğrulama kodları tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('verification_id')->comment('Doğrulama benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('account_id')->comment('Hesap kimliği');
            $table->enum('type', ['email', 'sms', '2fa'])->default('email')->comment('Doğrulama tipi');
            $table->string('code', 20)->comment('Doğrulama kodu');
            $table->string('recipient', 255)->comment('Kodun gönderildiği adres/numara');
            $table->timestamp('expires_at')->comment('Kodun geçerlilik bitişi');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            
            $table->index(['account_id', 'type', 'code'], 'idx_verification_check');
        });

        Schema::create(self::TABLE_ACCOUNT_ACCESS_TOKEN, function (Blueprint $table) {
            $table->comment('API erişim jetonları (Token) tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('access_token_id')->comment('Token benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->morphs('tokenable'); // tokenable_id, tokenable_type
            $table->string('name', 255)->comment('Token adı/cihaz adı');
            $table->string('token', 64)->unique()->comment('Erişim anahtarı');
            $table->text('abilities')->nullable()->comment('Yetkiler (Scope)');
            $table->timestamp('last_used_at')->nullable()->comment('Son kullanım');
            $table->timestamp('expires_at')->nullable()->comment('Bitiş zamanı');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_ACCOUNT_ACCESS_TOKEN);
        Schema::dropIfExists(self::TABLE_ACCOUNT_VERIFICATION);
        Schema::dropIfExists(self::TABLE_ACCOUNT_PREORDER);
        Schema::dropIfExists(self::TABLE_ACCOUNT_PRICE_ALERT);
        Schema::dropIfExists(self::TABLE_ACCOUNT_WISHLIST_ITEM);
        Schema::dropIfExists(self::TABLE_ACCOUNT_WISHLIST);
        Schema::dropIfExists(self::TABLE_ACCOUNT_REWARD);
        Schema::dropIfExists(self::TABLE_ACCOUNT_DOWNLOAD);
        Schema::dropIfExists(self::TABLE_ACCOUNT_BANK);
        Schema::dropIfExists(self::TABLE_ACCOUNT_CONTACT);
        Schema::dropIfExists(self::TABLE_ACCOUNT_AUTHORIZED);
        Schema::dropIfExists(self::TABLE_ACCOUNT_PASSWORD_RESET);
        Schema::dropIfExists(self::TABLE_ACCOUNT);
    }
};
