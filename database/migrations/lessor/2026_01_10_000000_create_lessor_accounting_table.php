<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_lsr';

    private const SAFE_TABLE = 'accntng_safe';
    private const SAFE_BANK_TABLE = 'accntng_safe_bank';
    private const SAFE_ACTIVITY_TABLE = 'accntng_safe_activity';
    private const LOAN_TABLE = 'accntng_loan';
    private const CHECK_TABLE = 'accntng_check';
    private const PERSONNEL_TABLE = 'accntng_personnel';
    private const PERSONNEL_SALARY_TABLE = 'accntng_personnel_salary';
    private const PERSONNEL_CONTACT_TABLE = 'accntng_personnel_contact';
    private const PERSONNEL_BANK_ACCOUNT_TABLE = 'accntng_personnel_bank_account';
    private const PERSONNEL_DOCUMENT_TABLE = 'accntng_personnel_document';
    private const ORDER_TABLE = 'accntng_order';
    private const ORDER_ITEM_TABLE = 'accntng_order_item';
    private const ORDER_FINANCIAL_TABLE = 'accntng_order_financial';
    private const ORDER_CONTACT_TABLE = 'accntng_order_contact';
    private const ORDER_ACTIVITY_TABLE = 'accntng_order_activity';
    private const INVOICE_TABLE = 'accntng_invoice';
    private const INVOICE_ITEM_TABLE = 'accntng_invoice_item';
    private const INVOICE_FINANCIAL_TABLE = 'accntng_invoice_financial';
    private const INVOICE_CONTACT_TABLE = 'accntng_invoice_contact';
    private const RECURRING_TABLE = 'accntng_recurring';

    public function up(): void
    {
        Schema::create(self::SAFE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Sistemdeki kasaları (nakit kasaları, banka hesapları) tanımlar.');

            $table->bigIncrements('safe_id')->comment('Kasa için birincil anahtar');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Kasa tipi ID\'si (örn: Nakit, Banka)');
            $table->string('code', 50)->unique()->comment('Kasa için benzersiz kod');
            $table->string('name', 255)->comment('Kasa adı');
            $table->string('description', 500)->nullable()->comment('Kasa açıklaması');
            $table->string('currency_code', 3)->comment('Kasanın para birimi kodu (örn: TRY, USD)');
            $table->boolean('status')->default(false)->comment('Kasanın durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('type_id', 'idx_acc_safe_type_id');
            $table->index('code', 'idx_acc_safe_code');
            $table->index('currency_code', 'idx_acc_safe_curr_code');
            $table->index('status', 'idx_acc_safe_status');
        });

        Schema::create(self::SAFE_BANK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasa tipi banka olan kasaların banka hesabı detaylarını saklar.');

            $table->bigIncrements('safe_bank_id')->comment('Kasa banka hesabı için birincil anahtar');
            $table->unsignedBigInteger('safe_id')->comment('İlgili kasa ID\'si');
            $table->unsignedBigInteger('bank_id')->default(0)->comment('İlgili banka ID\'si (örn: Ziraat Bankası ID)');
            $table->string('type', 50)->nullable()->comment('Banka hesabı tipi (örn: Vadesiz TL, Vadesiz USD)');
            $table->string('name', 255)->comment('Banka hesabı adı (örn: Şirket TL Hesabı)');
            $table->string('owner', 255)->comment('Hesap sahibinin adı/unvanı');
            $table->string('account_number', 50)->nullable()->comment('Hesap numarası');
            $table->string('branch_code', 20)->nullable()->comment('Şube kodu');
            $table->string('iban_number', 34)->unique()->comment('IBAN numarası (benzersiz, max 34 karakter)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('safe_id', 'idx_acc_safe_bank_safe_id');
            $table->index('bank_id', 'idx_acc_safe_bank_bank_id');
            $table->index('iban_number', 'idx_acc_safe_bank_iban');
        });

        Schema::create(self::SAFE_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kasalar üzerindeki finansal hareketleri (giriş/çıkış) saklar.');

            $table->bigIncrements('safe_activity_id')->comment('Kasa hareketi için birincil anahtar');
            $table->unsignedBigInteger('safe_id')->comment('İlgili kasa ID\'si');
            $table->unsignedBigInteger('process_id')->default(0)->comment('İşlem tipi ID\'si (örn: Ödeme, Tahsilat, Gider)');
            $table->unsignedBigInteger('module_group_id')->default(0)->nullable()->comment('İlgili modül grubunun ID\'si (örn: Sipariş, Fatura)');
            $table->unsignedBigInteger('module_type_id')->default(0)->nullable()->comment('İlgili modül tipinin ID\'si');
            $table->unsignedBigInteger('module_id')->default(0)->nullable()->comment('İlgili modül kaydının ID\'si (örn: order_id, invoice_id)');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('İlgili hesap ID\'si (ödeme yapan/alan)');
            $table->unsignedBigInteger('branch_id')->default(0)->nullable()->comment('İlgili şube ID\'si (def_co_branch)');
            $table->decimal('amount', 19, 4)->comment('İşlem miktarı (Pozitif: Giriş, Negatif: Çıkış)');
            $table->string('code', 100)->nullable()->comment('İşlem için benzersiz kod/referans');
            $table->string('name', 255)->nullable()->comment('İşlem adı/kısa açıklaması');
            $table->text('description')->nullable()->comment('İşlemin detaylı açıklaması');
            $table->longText('content')->nullable()->comment('İşleme ait ek JSON verileri (örn: ödeme gateway yanıtı)');
            $table->dateTime('date_activity')->comment('İşlemin gerçekleştiği tarih ve saat');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('safe_id', 'idx_acc_safe_act_safe_id');
            $table->index('process_id', 'idx_acc_safe_act_proc_id');
            $table->index('module_id', 'idx_acc_safe_act_mod_id');
            $table->index('account_id', 'idx_acc_safe_act_acc_id');
            $table->index('date_activity', 'idx_acc_safe_act_date_act');
        });

        Schema::create(self::LOAN_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Kısa/uzun vadeli borç ve alacakları (kredileri) saklar.');

            $table->bigIncrements('loan_id')->comment('Kredi/borç için birincil anahtar');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Kredi tipi ID\'si (örn: Verilen Kredi, Alınan Kredi)');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('İlgili hesap ID\'si (kredi verilen/alınan kişi/kurum)');
            $table->unsignedBigInteger('module_group_id')->default(0)->nullable()->comment('İlgili modül grubunun ID\'si');
            $table->unsignedBigInteger('module_type_id')->default(0)->nullable()->comment('İlgili modül tipinin ID\'si');
            $table->unsignedBigInteger('module_id')->default(0)->nullable()->comment('İlgili modül kaydının ID\'si');
            $table->string('code', 100)->unique()->comment('Kredi/borç için benzersiz kod');
            $table->string('name', 255)->comment('Kredi/borcun adı/konusu');
            $table->string('description', 500)->nullable()->comment('Kredi/borcun detaylı açıklaması');
            $table->string('currency_code', 3)->comment('Para birimi kodu');
            $table->decimal('amount', 19, 4)->comment('Kredi/borcun ana miktarı');
            $table->boolean('status')->default(false)->comment('Kredi/borcun durumu (0: Açık, 1: Kapalı, 2: Gecikmeli)');
            $table->dateTime('date_due')->nullable()->comment('Vade tarihi ve saati');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('type_id', 'idx_acc_loan_type_id');
            $table->index('account_id', 'idx_acc_loan_acc_id');
            $table->index('code', 'idx_acc_loan_code');
            $table->index('status', 'idx_acc_loan_status');
            $table->index('date_due', 'idx_acc_loan_date_due');
        });

        Schema::create(self::CHECK_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Alınan veya verilen çekleri saklar.');

            $table->bigIncrements('check_id')->comment('Çek için birincil anahtar');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('Çekin ilişkili olduğu hesap ID\'si (çek sahibi)');
            $table->unsignedBigInteger('bank_id')->default(0)->comment('Çekin ait olduğu banka ID\'si');
            $table->string('bank_account_number', 50)->nullable()->comment('Çekin ait olduğu banka hesap numarası');
            $table->string('check_number', 50)->unique()->comment('Çek numarası (benzersiz)');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Çek tipi ID\'si (örn: Alınan Çek, Verilen Çek)');
            $table->string('code', 100)->nullable()->comment('Çek için benzersiz referans kodu');
            $table->decimal('amount', 19, 4)->comment('Çek miktarı');
            $table->string('currency_code', 3)->comment('Çek para birimi kodu');
            $table->date('date_delivery')->nullable()->comment('Çekin teslim alındığı/edildiği tarih');
            $table->date('date_expiry')->nullable()->comment('Çekin vade/geçerlilik tarihi');
            $table->string('name', 255)->comment('Çekin adı/konusu');
            $table->string('description', 500)->nullable()->comment('Çekin açıklaması');
            $table->boolean('status')->default(false)->comment('Çekin durumu (0: Açık, 1: Tahsil Edildi, 2: İade Edildi)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('account_id', 'idx_acc_check_acc_id');
            $table->index('bank_id', 'idx_acc_check_bank_id');
            $table->index('check_number', 'idx_acc_check_num');
            $table->index('type_id', 'idx_acc_check_type_id');
            $table->index('status', 'idx_acc_check_status');
            $table->index('date_expiry', 'idx_acc_check_date_expiry');
        });

        Schema::create(self::PERSONNEL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personel (çalışan) bilgilerini saklar.');

            $table->bigIncrements('personnel_id')->comment('Personel için birincil anahtar');
            $table->unsignedBigInteger('department_id')->default(0)->nullable()->comment('Personelin çalıştığı departman ID\'si (def_co_department)');
            $table->string('code', 50)->unique()->nullable()->comment('Personel için benzersiz kod/sicil numarası');
            $table->string('title', 100)->nullable()->comment('Personelin ünvanı (örn: Yazılım Mühendisi, Satış Direktörü)');
            $table->string('firstname', 100)->comment('Personelin adı');
            $table->string('lastname', 100)->comment('Personelin soyadı');
            $table->string('nationality', 100)->nullable()->comment('Personelin uyruğu');
            $table->string('citizenship_number', 20)->unique()->nullable()->comment('T.C. Kimlik Numarası veya eşdeğeri');
            $table->string('health_insurance_number', 50)->nullable()->comment('Sağlık sigorta numarası');
            $table->string('image', 255)->nullable()->comment('Personel profil görseli URL/dosya yolu');
            $table->enum('gender', ['male', 'female', 'other'])->comment('Cinsiyet');
            $table->date('start_date')->comment('İşe başlama tarihi');
            $table->date('end_date')->nullable()->comment('İşten ayrılma tarihi (varsa)');
            $table->string('end_reason', 255)->nullable()->comment('İşten ayrılma nedeni');
            $table->date('birth_date')->comment('Doğum tarihi');
            $table->unsignedBigInteger('default_contact_id')->default(0)->nullable()->comment('Varsayılan iletişim adresi ID\'si');
            $table->unsignedBigInteger('default_bank_account_id')->default(0)->nullable()->comment('Varsayılan banka hesabı ID\'si');
            $table->boolean('status')->default(0)->comment('Personelin durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('department_id', 'idx_acc_pers_dept_id');
            $table->index('code', 'idx_acc_pers_code');
            $table->index('citizenship_number', 'idx_acc_pers_citizen_num');
            $table->index('status', 'idx_acc_pers_status');
            $table->index('start_date', 'idx_acc_pers_start_date');
        });

        Schema::create(self::PERSONNEL_SALARY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personelin maaş geçmişini ve detaylarını saklar.');

            $table->bigIncrements('personnel_salary_id')->comment('Personel maaş kaydı için birincil anahtar');
            $table->unsignedBigInteger('personnel_id')->comment('İlgili personel ID\'si');
            $table->decimal('amount', 19, 4)->comment('Maaş miktarı');
            $table->unsignedInteger('start_year')->default(0)->comment('Maaşın başlangıç yılı');
            $table->unsignedTinyInteger('start_month')->default(0)->comment('Maaşın başlangıç ayı');
            $table->unsignedTinyInteger('start_day')->default(0)->comment('Maaşın başlangıç günü');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('personnel_id', 'idx_acc_pers_salary_pers_id');
            $table->index(['start_year', 'start_month'], 'idx_acc_pers_salary_start_date');
        });

        Schema::create(self::PERSONNEL_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personele ait iletişim ve adres bilgilerini saklar.');

            $table->bigIncrements('personnel_contact_id')->comment('Personel iletişim kaydı için birincil anahtar');
            $table->unsignedBigInteger('personnel_id')->comment('İlgili personel ID\'si');
            $table->unsignedBigInteger('address_type_id')->default(0)->comment('Adres tipi ID\'si (def_loc_address_type)');
            $table->string('code', 100)->nullable()->comment('İletişim kaydı için benzersiz kod');
            $table->string('email', 255)->nullable()->comment('E-posta adresi');
            $table->string('website', 255)->nullable()->comment('Web sitesi adresi');
            $table->string('telephone_1', 50)->nullable()->comment('Telefon numarası 1');
            $table->string('telephone_2', 50)->nullable()->comment('Telefon numarası 2');
            $table->string('address_1', 255)->comment('Adres satırı 1');
            $table->string('address_2', 255)->nullable()->comment('Adres satırı 2');
            $table->unsignedBigInteger('country_id')->comment('Ülke ID\'si (def_loc_country)');
            $table->unsignedBigInteger('city_id')->comment('Şehir ID\'si (def_loc_city)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe ID\'si (def_loc_district)');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->boolean('status')->default(0)->comment('İletişim kaydının durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('personnel_id', 'idx_acc_pers_cont_pers_id');
            $table->index('address_type_id', 'idx_acc_pers_cont_addr_type_id');
            $table->index('country_id', 'idx_acc_pers_cont_country_id');
            $table->index('city_id', 'idx_acc_pers_cont_city_id');
            $table->index('status', 'idx_acc_pers_cont_status');
        });

        Schema::create(self::PERSONNEL_BANK_ACCOUNT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personele ait banka hesabı bilgilerini saklar.');

            $table->bigIncrements('personnel_bank_account_id')->comment('Personel banka hesabı için birincil anahtar');
            $table->unsignedBigInteger('personnel_id')->comment('İlgili personel ID\'si');
            $table->unsignedBigInteger('bank_id')->default(0)->nullable()->comment('İlgili banka ID\'si');
            $table->string('currency_code', 3)->nullable()->comment('Hesabın para birimi kodu');
            $table->string('code', 100)->nullable()->comment('Banka hesabı için benzersiz kod');
            $table->string('type', 50)->nullable()->comment('Hesap tipi (örn: Vadesiz, Ortak Hesap)');
            $table->string('name', 255)->comment('Banka hesabının adı');
            $table->string('owner', 255)->comment('Hesap sahibi adı/unvanı');
            $table->string('account_number', 50)->nullable()->comment('Hesap numarası');
            $table->string('branch_code', 20)->nullable()->comment('Şube kodu');
            $table->string('iban_number', 34)->unique()->comment('IBAN numarası (benzersiz, max 34 karakter)');
            $table->boolean('status')->default(0)->comment('Banka hesabının durumu (0: Pasif, 1: Aktif)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('personnel_id', 'idx_acc_pers_bank_acc_pers_id');
            $table->index('bank_id', 'idx_acc_pers_bank_acc_bank_id');
            $table->index('iban_number', 'idx_acc_pers_bank_acc_iban');
            $table->index('status', 'idx_acc_pers_bank_acc_status');
        });

        Schema::create(self::PERSONNEL_DOCUMENT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Personele ait belgeleri (sözleşme, kimlik, diploma) saklar.');

            $table->bigIncrements('personnel_document_id')->comment('Personel belgesi için birincil anahtar');
            $table->unsignedBigInteger('personnel_id')->comment('İlgili personel ID\'si');
            $table->enum('type', ['legal', 'special'])->comment('Belge tipi (Yasal, Özel)');
            $table->string('code', 100)->nullable()->comment('Belge için benzersiz kod/referans');
            $table->string('name', 255)->comment('Belge adı');
            $table->string('description', 500)->nullable()->comment('Belge açıklaması');
            $table->string('file', 255)->nullable()->comment('Belge dosyasının yolu/URL\'si');
            $table->boolean('status')->default(0)->comment('Belgenin durumu (0: Geçersiz, 1: Geçerli)');
            $table->dateTime('date_validity')->nullable()->comment('Belgenin geçerlilik bitiş tarihi (varsa)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('personnel_id', 'idx_acc_pers_doc_pers_id');
            $table->index('type', 'idx_acc_pers_doc_type');
            $table->index('status', 'idx_acc_pers_doc_status');
        });

        Schema::create(self::ORDER_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Müşteri siparişlerinin ana bilgilerini saklar.');

            $table->bigIncrements('order_id')->comment('Sipariş için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Sipariş için benzersiz kod');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('Sipariş veren müşteri/hesap ID\'si');
            $table->string('account_name', 255)->nullable()->comment('Müşteri adı (hızlı erişim için)');
            $table->unsignedBigInteger('account_group_id')->default(0)->nullable()->comment('Müşterinin ait olduğu grup ID\'si');
            $table->unsignedBigInteger('operation_id')->default(0)->comment('Siparişin son operasyon/işlem tipi ID\'si (örn: Oluşturuldu, Ödendi, Kargoya Verildi)');
            $table->unsignedBigInteger('status_id')->default(0)->comment('Siparişin son durum ID\'si (örn: Beklemede, Tamamlandı)');
            $table->string('language_code', 10)->nullable()->comment('Siparişin verildiği dil kodu');
            $table->string('currency_code', 3)->nullable()->comment('Siparişin para birimi kodu');
            $table->string('payment_method', 50)->nullable()->comment('Kullanılan ödeme metodu (örn: Kredi Kartı, Kapıda Ödeme)');
            $table->unsignedBigInteger('payment_contact_id')->default(0)->nullable()->comment('Ödeme için kullanılan iletişim adresi ID\'si');
            $table->string('payment_origin', 100)->nullable()->comment('Ödeme kaynağı/kanalı (örn: Website, Mobil Uygulama, Pazaryeri)');
            $table->string('coupon_code', 100)->nullable()->comment('Kullanılan kupon kodu (varsa)');
            $table->string('voucher_code', 100)->nullable()->comment('Kullanılan hediye çeki kodu (varsa)');
            $table->string('reward_point', 100)->nullable()->comment('Kullanılan ödül puanı miktarı (string olarak?)');
            $table->longText('comment')->nullable()->comment('Müşteri notları veya siparişe ek yorumlar');
            $table->unsignedBigInteger('recurring_id')->default(0)->nullable()->comment('Tekrarlayan sipariş (abonelik) ID\'si (varsa)');
            $table->string('user_agent', 255)->nullable()->comment('Siparişin verildiği tarayıcı/cihaz bilgisi');
            $table->string('accept_language', 255)->nullable()->comment('Kullanıcının tercih ettiği dil ayarları');
            $table->string('ip_modified', 45)->nullable()->comment('Siparişin son güncellendiği IP adresi');
            $table->string('ip_created', 45)->nullable()->comment('Siparişin oluşturulduğu IP adresi');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_acc_order_code');
            $table->index('account_id', 'idx_acc_order_acc_id');
            $table->index('status_id', 'idx_acc_order_status_id');
            $table->index('payment_method', 'idx_acc_order_pay_meth');
        });

        Schema::create(self::ORDER_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Siparişlerdeki ürün/hizmet kalemlerini saklar.');

            $table->bigIncrements('order_item_id')->comment('Sipariş kalemi için birincil anahtar');
            $table->unsignedBigInteger('order_id')->comment('İlgili sipariş ID\'si');
            $table->unsignedBigInteger('product_id')->default(0)->nullable()->comment('Sipariş edilen ürün ID\'si (varsa)');
            $table->unsignedBigInteger('recurring_id')->default(0)->nullable()->comment('Tekrarlayan sipariş öğesi ID\'si (varsa)');
            $table->unsignedBigInteger('variant_stock_id')->default(0)->nullable()->comment('Ürün varyant stok ID\'si (varsa)');
            $table->string('product_code', 100)->nullable()->comment('Ürün kodu');
            $table->string('product_name', 255)->comment('Ürün adı');
            $table->decimal('product_price', 19, 4)->comment('Tekil ürün fiyatı (indirimler hariç)');
            $table->decimal('exchange_rate', 15, 8)->default(1.0)->comment('Para birimi dönüşüm oranı');
            $table->string('currency_code', 3)->comment('Ürünün para birimi kodu');
            $table->unsignedInteger('quantity')->default(0)->comment('Ürün miktarı');
            $table->unsignedBigInteger('unit_id')->default(0)->nullable()->comment('Ürünün birim ID\'si (örn: adet, kg)');
            $table->unsignedBigInteger('tax_class_id')->default(0)->nullable()->comment('Ürünün uygulanan vergi sınıfı ID\'si');
            $table->string('discount_type', 50)->nullable()->comment('Ürüne uygulanan indirim tipi (percentage, amount)');
            $table->decimal('discount_value', 19, 4)->default(0.0000)->comment('Uygulanan indirim değeri');
            $table->longText('options')->nullable()->comment('Ürün varyant/seçenek bilgilerinin JSON formatı');
            $table->longText('groupeds')->nullable()->comment('Grup ürün detaylarının JSON formatı');
            $table->longText('bundles')->nullable()->comment('Paket ürün detaylarının JSON formatı');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('order_id', 'idx_acc_order_item_order_id');
            $table->index('product_id', 'idx_acc_order_item_prod_id');
            $table->index('product_code', 'idx_acc_order_item_prod_code');
        });

        Schema::create(self::ORDER_FINANCIAL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Siparişlerin finansal özetlerini (alt toplamlar, vergiler, kargo vb.) saklar.');

            $table->bigIncrements('order_financial_id')->comment('Sipariş finansal kaydı için birincil anahtar');
            $table->unsignedBigInteger('order_id')->comment('İlgili sipariş ID\'si');
            $table->string('title', 255)->comment('Finansal kalemin başlığı (örn: Ara Toplam, Kargo, KDV)');
            $table->string('code', 50)->nullable()->comment('Finansal kalemin kodu (örn: sub_total, shipping_fee)');
            $table->string('type', 50)->comment('Finansal kalemin tipi (örn: total, tax, discount)');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sırası');
            $table->boolean('removable')->default(false)->comment('Kalem kaldırılabilir mi?');
            $table->decimal('value', 19, 4)->comment('Kalemin nicel değeri (örn: indirim oranı)');
            $table->decimal('amount', 19, 4)->comment('Kalemin finansal tutarı');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('order_id', 'idx_acc_order_fin_order_id');
            $table->index('code', 'idx_acc_order_fin_code');
            $table->index('type', 'idx_acc_order_fin_type');
            $table->index('sort_order', 'idx_acc_order_fin_sort_order');
        });

        Schema::create(self::ORDER_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Siparişle ilişkili fatura ve teslimat adresi bilgilerini saklar.');

            $table->bigIncrements('order_contact_id')->comment('Sipariş iletişim kaydı için birincil anahtar');
            $table->unsignedBigInteger('order_id')->comment('İlgili sipariş ID\'si');
            $table->unsignedBigInteger('taxpayer_type_id')->default(0)->nullable()->comment('Mükellef tipi ID\'si');
            $table->string('operation_type', 50)->comment('İletişim tipi (örn: payment, shipping)');
            $table->unsignedBigInteger('address_type_id')->default(0)->nullable()->comment('Adres tipi ID\'si (def_loc_address_type)');
            $table->string('title', 100)->nullable()->comment('Adres başlığı (örn: Fatura Adresi, Teslimat Adresi)');
            $table->string('firstname', 100)->nullable()->comment('Adres sahibinin adı');
            $table->string('lastname', 100)->nullable()->comment('Adres sahibinin soyadı');
            $table->string('citizenship_number', 20)->nullable()->comment('T.C. Kimlik Numarası');
            $table->string('company', 255)->nullable()->comment('Şirket adı');
            $table->string('tax_office', 255)->nullable()->comment('Vergi dairesi');
            $table->string('tax_number', 50)->nullable()->comment('Vergi numarası');
            $table->string('email', 255)->nullable()->comment('E-posta adresi');
            $table->string('telephone', 50)->nullable()->comment('Telefon numarası');
            $table->string('address_1', 255)->comment('Adres satırı 1');
            $table->string('address_2', 255)->nullable()->comment('Adres satırı 2');
            $table->unsignedBigInteger('country_id')->comment('Ülke ID\'si');
            $table->unsignedBigInteger('city_id')->comment('Şehir ID\'si');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe ID\'si');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('order_id', 'idx_acc_order_cont_order_id');
            $table->index('operation_type', 'idx_acc_order_cont_op_type');
            $table->index('address_type_id', 'idx_acc_order_cont_addr_type_id');
        });

        Schema::create(self::ORDER_ACTIVITY_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Siparişlerin durum değişikliklerini ve diğer aktiviteleri saklar.');

            $table->bigIncrements('order_activity_id')->comment('Sipariş aktivitesi için birincil anahtar');
            $table->unsignedBigInteger('order_id')->comment('İlgili sipariş ID\'si');
            $table->unsignedBigInteger('operation_id')->default(0)->nullable()->comment('Gerçekleşen operasyon tipi ID\'si (örn: Ödeme Alındı, Kargoya Verildi)');
            $table->unsignedBigInteger('status_id')->default(0)->nullable()->comment('Yeni sipariş durumu ID\'si');
            $table->boolean('notification')->default(false)->comment('Müşteriye bildirim gönderildi mi?');
            $table->string('comment', 500)->nullable()->comment('Aktiviteye ilişkin yorum veya not');
            $table->longText('payment_operation')->nullable()->comment('Ödeme operasyonuyla ilgili JSON verisi (ödeme gateway yanıtı vb.)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('order_id', 'idx_acc_order_act_order_id');
            $table->index('operation_id', 'idx_acc_order_act_op_id');
            $table->index('status_id', 'idx_acc_order_act_status_id');
        });

        Schema::create(self::INVOICE_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Faturaların ana bilgilerini saklar (alış ve satış).');

            $table->bigIncrements('invoice_id')->comment('Fatura için birincil anahtar');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Fatura tipi ID\'si (örn: Satış Faturası, Alış Faturası)');
            $table->string('group_code', 50)->nullable()->comment('Fatura grubunun kodu (örn: e-fatura, kağıt fatura)');
            $table->string('currency_code', 3)->comment('Fatura para birimi kodu');
            $table->string('language_code', 10)->nullable()->comment('Faturanın dili kodu');
            $table->string('code', 100)->unique()->comment('Fatura için benzersiz kod/numara');
            $table->string('title', 255)->comment('Fatura başlığı');
            $table->unsignedBigInteger('category_id')->default(0)->nullable()->comment('Faturanın ait olduğu kategori ID\'si (örn: Hizmet, Ürün)');
            $table->string('tag', 255)->nullable()->comment('Fatura etiketleri (virgülle ayrılmış)');
            $table->longText('description')->nullable()->comment('Fatura açıklaması/notlar');
            $table->string('entry_date_time', 50)->comment('Fatura giriş tarih ve saati (metin olarak saklanıyor)');
            $table->string('entry_serial', 20)->nullable()->comment('Fatura seri numarası (örn: ABC)');
            $table->string('entry_queue', 50)->nullable()->comment('Fatura sıra numarası (örn: 0001234)');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('Fatura kesilen/gönderilen hesap ID\'si');
            $table->string('account_name', 255)->nullable()->comment('Hesap adı (hızlı erişim için)');
            $table->unsignedBigInteger('account_group_id')->default(0)->nullable()->comment('Hesabın ait olduğu grup ID\'si');
            $table->unsignedBigInteger('account_contact_id')->default(0)->nullable()->comment('Hesabın ilgili iletişim adresi ID\'si');
            $table->boolean('account_abroad')->default(false)->comment('Hesabın yurt dışı olup olmadığı');
            $table->string('tax_office', 255)->nullable()->comment('Vergi dairesi');
            $table->string('tax_number', 50)->nullable()->comment('Vergi numarası');
            $table->dateTime('payment_due_date')->nullable()->comment('Ödeme vadesi tarihi ve saati');
            $table->boolean('waybill_status')->default(0)->comment('Fatura ile sevk irsaliyesi ilişkisi (0: Yok, 1: Mevcut)');
            $table->dateTime('waybill_date')->nullable()->comment('Sevk irsaliyesi tarihi');
            $table->string('waybill_number', 100)->nullable()->comment('Sevk irsaliyesi numarası');
            $table->unsignedBigInteger('order_id')->default(0)->nullable()->comment('İlgili sipariş ID\'si (varsa)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('type_id', 'idx_acc_inv_type_id');
            $table->index('code', 'idx_acc_inv_code');
            $table->index('account_id', 'idx_acc_inv_acc_id');
            $table->index('order_id', 'idx_acc_inv_order_id');
            $table->index('payment_due_date', 'idx_acc_inv_pay_due_date');
        });

        Schema::create(self::INVOICE_ITEM_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura kalemlerini (ürün/hizmet) saklar.');

            $table->bigIncrements('invoice_item_id')->comment('Fatura kalemi için birincil anahtar');
            $table->unsignedBigInteger('invoice_id')->comment('İlgili fatura ID\'si');
            $table->unsignedBigInteger('product_id')->default(0)->nullable()->comment('Ürün ID\'si (varsa)');
            $table->string('product_name', 255)->comment('Ürün/Hizmet adı');
            $table->decimal('product_price', 19, 4)->comment('Tekil ürün/hizmet fiyatı');
            $table->decimal('exchange_rate', 15, 8)->default(1.0)->comment('Para birimi dönüşüm oranı');
            $table->string('currency_code', 3)->comment('Para birimi kodu');
            $table->unsignedInteger('quantity')->default(0)->comment('Miktar');
            $table->unsignedBigInteger('unit_id')->default(0)->nullable()->comment('Birim ID\'si');
            $table->unsignedBigInteger('tax_class_id')->default(0)->nullable()->comment('Vergi sınıfı ID\'si');
            $table->string('discount_type', 50)->nullable()->comment('İndirim tipi');
            $table->decimal('discount_value', 19, 4)->default(0.0000)->comment('İndirim değeri');
            $table->string('excise_duty_type', 50)->nullable()->comment('ÖTV tipi (varsa)');
            $table->unsignedTinyInteger('excise_duty_rate')->default(0)->nullable()->comment('ÖTV oranı (%)');
            $table->unsignedTinyInteger('communications_tax_rate')->default(0)->nullable()->comment('İletişim vergisi oranı (%)');
            $table->string('delivery_method', 50)->nullable()->comment('Teslimat metodu');
            $table->string('shipping_method', 50)->nullable()->comment('Kargo metodu');
            $table->string('variant', 255)->nullable()->comment('Ürün varyant detayları (JSON veya metin)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('invoice_id', 'idx_acc_inv_item_inv_id');
            $table->index('product_id', 'idx_acc_inv_item_prod_id');
            $table->index('tax_class_id', 'idx_acc_inv_item_tax_class_id');
        });

        Schema::create(self::INVOICE_FINANCIAL_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Faturaların finansal özetlerini saklar.');

            $table->bigIncrements('invoice_financial_id')->comment('Fatura finansal kaydı için birincil anahtar');
            $table->unsignedBigInteger('invoice_id')->comment('İlgili fatura ID\'si');
            $table->string('title', 255)->comment('Finansal kalemin başlığı');
            $table->string('code', 50)->nullable()->comment('Finansal kalemin kodu');
            $table->string('type', 50)->comment('Finansal kalemin tipi');
            $table->integer('sort_order')->default(0)->comment('Görüntüleme sırası');
            $table->decimal('amount', 19, 4)->comment('Kalemin finansal tutarı');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('invoice_id', 'idx_acc_inv_fin_inv_id');
            $table->index('code', 'idx_acc_inv_fin_code');
            $table->index('type', 'idx_acc_inv_fin_type');
            $table->index('sort_order', 'idx_acc_inv_fin_sort_order');
        });

        Schema::create(self::INVOICE_CONTACT_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Faturayla ilişkili iletişim ve adres bilgilerini saklar.');

            $table->bigIncrements('invoice_contact_id')->comment('Fatura iletişim kaydı için birincil anahtar');
            $table->unsignedBigInteger('invoice_id')->comment('İlgili fatura ID\'si');
            $table->string('operation_type', 50)->comment('İletişim tipi (örn: billing, delivery)');
            $table->unsignedBigInteger('address_type_id')->default(0)->nullable()->comment('Adres tipi ID\'si');
            $table->string('title', 100)->nullable()->comment('Adres başlığı');
            $table->string('firstname', 100)->nullable()->comment('Adres sahibinin adı');
            $table->string('lastname', 100)->nullable()->comment('Adres sahibinin soyadı');
            $table->string('company', 255)->nullable()->comment('Şirket adı');
            $table->string('tax_office', 255)->nullable()->comment('Vergi dairesi');
            $table->string('tax_number', 50)->nullable()->comment('Vergi numarası');
            $table->string('email', 255)->nullable()->comment('E-posta adresi');
            $table->string('telephone', 50)->nullable()->comment('Telefon numarası');
            $table->string('address_1', 255)->comment('Adres satırı 1');
            $table->string('address_2', 255)->nullable()->comment('Adres satırı 2');
            $table->unsignedBigInteger('country_id')->comment('Ülke ID\'si');
            $table->unsignedBigInteger('city_id')->comment('Şehir ID\'si');
            $table->unsignedBigInteger('district_id')->nullable()->comment('İlçe ID\'si');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('invoice_id', 'idx_acc_inv_cont_inv_id');
            $table->index('operation_type', 'idx_acc_inv_cont_op_type');
            $table->index('address_type_id', 'idx_acc_inv_cont_addr_type_id');
        });

        Schema::create(self::RECURRING_TABLE, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Tekrarlayan ödeme planlarını veya abonelikleri saklar.');

            $table->bigIncrements('recurring_id')->comment('Tekrarlayan kayıt için birincil anahtar');
            $table->string('code', 100)->unique()->comment('Tekrarlayan kayıt için benzersiz kod');
            $table->unsignedBigInteger('recurring_type_id')->default(0)->comment('Tekrarlayan tip ID\'si (örn: Aylık Abonelik, Yıllık Lisans)');
            $table->unsignedBigInteger('account_id')->default(0)->nullable()->comment('İlgili hesap ID\'si (abone olan müşteri)');
            $table->unsignedBigInteger('access_id')->default(0)->nullable()->comment('Hesabın erişim bilgisi ID\'si (wbst_account_access)');
            $table->unsignedBigInteger('product_id')->default(0)->nullable()->comment('Tekrarlayan ürün/hizmet ID\'si');
            $table->unsignedInteger('quantity')->default(0)->comment('Miktar');
            $table->decimal('product_price', 19, 4)->comment('Tekrarlayan ürünün/hizmetin fiyatı');
            $table->string('currency_code', 3)->comment('Para birimi kodu');
            $table->unsignedBigInteger('tax_class_id')->default(0)->nullable()->comment('Vergi sınıfı ID\'si');
            $table->integer('trial')->default(0)->comment('Deneme süresi (gün olarak veya başka birim)');
            $table->dateTime('date_start')->nullable()->comment('Tekrarlayan başlangıç tarihi ve saati');
            $table->dateTime('date_end')->nullable()->comment('Tekrarlayan bitiş tarihi ve saati (varsa)');
            $table->string('user_agent', 255)->nullable()->comment('Kullanıcının tarayıcı/cihaz bilgisi');
            $table->string('accept_language', 255)->nullable()->comment('Kullanıcının tercih ettiği dil ayarları');
            $table->string('ip_created', 45)->nullable()->comment('Kaydın oluşturulduğu IP adresi');
            $table->boolean('status')->default(false)->comment('Tekrarlayan durum (0: Pasif, 1: Aktif, 2: Duraklatıldı)');
            $table->timestamp('created_at')->useCurrent()->comment('Kaydın oluşturulma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kaydın son güncellenme zamanı');

            $table->index('code', 'idx_acc_rec_code');
            $table->index('recurring_type_id', 'idx_acc_rec_type_id');
            $table->index('account_id', 'idx_acc_rec_acc_id');
            $table->index('status', 'idx_acc_rec_status');
            $table->index('date_start', 'idx_acc_rec_date_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::RECURRING_TABLE);

        Schema::dropIfExists(self::INVOICE_CONTACT_TABLE);
        Schema::dropIfExists(self::INVOICE_FINANCIAL_TABLE);
        Schema::dropIfExists(self::INVOICE_ITEM_TABLE);
        Schema::dropIfExists(self::INVOICE_TABLE);

        Schema::dropIfExists(self::ORDER_ACTIVITY_TABLE);
        Schema::dropIfExists(self::ORDER_CONTACT_TABLE);
        Schema::dropIfExists(self::ORDER_FINANCIAL_TABLE);
        Schema::dropIfExists(self::ORDER_ITEM_TABLE);
        Schema::dropIfExists(self::ORDER_TABLE);

        Schema::dropIfExists(self::PERSONNEL_DOCUMENT_TABLE);
        Schema::dropIfExists(self::PERSONNEL_BANK_ACCOUNT_TABLE);
        Schema::dropIfExists(self::PERSONNEL_CONTACT_TABLE);
        Schema::dropIfExists(self::PERSONNEL_SALARY_TABLE);
        Schema::dropIfExists(self::PERSONNEL_TABLE);

        Schema::dropIfExists(self::CHECK_TABLE);
        Schema::dropIfExists(self::LOAN_TABLE);

        Schema::dropIfExists(self::SAFE_ACTIVITY_TABLE);
        Schema::dropIfExists(self::SAFE_BANK_TABLE);
        Schema::dropIfExists(self::SAFE_TABLE);
    }
};
