<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('acc_cash', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('cash_id');
			$table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
			$table->unsignedBigInteger('type_id')->default(0)->comment('Kasa türüne referans');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->string('currency_code', 255);
			$table->string('account_type', 255);
			$table->unsignedBigInteger('account_bank_id')->default(0)->nullable()->comment('Bankaya referans');
			$table->string('account_name', 255);
			$table->string('account_owner', 255);
			$table->string('account_number', 255);
			$table->string('account_branch_code', 255);
			$table->string('account_iban_number', 255);
			$table->string('account_swift_code', 255);
            $table->decimal('daily_limit', 15, 2)->nullable()->comment('Günlük işlem limiti');
            $table->decimal('minimum_balance', 15, 2)->nullable()->default(0)->comment('Minimum bakiye limiti');
            $table->decimal('maximum_balance', 15, 2)->nullable()->default(0)->comment('Maksimum bakiye limiti');
            $table->boolean('is_default')->default(false)->comment('Bu varsayılan kasa hesabı mı? (false: Hayır, true: Evet)');
            $table->boolean('status')->default(false)->comment('Kasa hesap durumu (false: Pasif, true: Aktif)');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_cash_transaction', function (Blueprint $table) {
			$table->comment('Kasa hesaplarla ilişkili tüm finansal işlemleri ve aktiviteleri; borç, alacak ve ilişkili meta veriler dahil olmak üzere takip eder. İş süreçleri ve modüllerle bağlantılı nakit hareketlerinin kapsamlı bir denetim izini tutar.');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('transaction_id')->comment('Kasa hesap aktivitesi için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->unsignedBigInteger('cash_id')->comment('Kasa hesaba referans');
            $table->unsignedBigInteger('flow_id')->comment('Süreç referansı');
            $table->unsignedBigInteger('group_id')->comment('Süreç grubuna referans');
            $table->unsignedBigInteger('type_id')->comment('Süreç türüne referans');
            $table->unsignedBigInteger('account_id')->comment('Hesaba referans');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('Şubeye referans');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('İşlemin ilişkili olduğu modül kaydının ID\'si (örn: Fatura ID, Sipariş ID)');
            $table->string('reference_code', 50)->nullable()->comment('İşlemin ilişkili olduğu modül kodu');
            $table->decimal('amount', 15, 2)->comment('Aktivite tutarı');
            $table->string('payment_method', 100)->default('cash')->comment('Ödeme yöntemi :cash, check, promissory_note, bank_transfer, credit_card');
            $table->timestamp('due_date')->nullable()->comment('Vade tarihi (çek/senet için)');
            $table->string('name', 255)->comment('Aktivite adı');
            $table->text('description')->nullable()->comment('Aktivite açıklaması');
            $table->text('content')->nullable()->comment('Ek içerik veya detaylar');
            $table->string('period', 7)->comment('Muhasebe dönemi YYYY-MM');
            $table->timestamp('date_activity')->useCurrent()->comment('Aktivite tarihi');
            $table->boolean('status')->default(false)->comment('Aktivite durumu (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID\'si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncellemeyi yapan kullanıcı ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');

            $table->index(['cash_id', 'date_activity'], 'idx_cash_transaction_cash_date');
            $table->index('account_id', 'idx_cash_transaction_account_id');
            $table->index('flow_id', 'idx_cash_transaction_flow_id');
            $table->index('group_id', 'idx_cash_transaction_group_id');
            $table->index('type_id', 'idx_cash_transaction_type_id');
            $table->index('reference_id', 'idx_cash_transaction_reference_id');
            $table->index('created_by', 'idx_cash_transaction_created_by');
        });
		
        Schema::create('acc_check', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('check_id');
			$table->integer('account_id')->default(0);
			$table->integer('bank_id')->default(0);
			$table->string('bank_account_number', 255);
			$table->string('check_number', 255);
			$table->integer('type_id')->default(0);
			$table->string('code', 255);
			$table->decimal('amount', 19, 2);
			$table->string('currency_code', 255);
			$table->date('date_delivery')->nullable()->default(null);
			$table->date('date_expiry')->nullable()->default(null);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_loan', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('loan_id');
			$table->integer('type_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->integer('module_group_id')->default(0);
			$table->integer('module_type_id')->default(0);
			$table->integer('module_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->string('currency_code', 255);
			$table->decimal('amount', 19, 2);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_due', 0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
	
        Schema::create('acc_demand', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('demand_id');
			$table->string('code', 255);
			$table->integer('product_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->string('firstname', 255);
			$table->string('lastname', 255);
			$table->string('company', 255);
			$table->string('email', 255);
			$table->string('telephone', 255);
			$table->longText('comment');
			$table->string('user_agent', 255);
			$table->string('accept_language', 255);
			$table->string('ip_created', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_employee', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('employee_id');
			$table->integer('department_id')->default(0)->nullable();
			$table->string('code', 255)->nullable();
			$table->string('title', 255)->nullable();
			$table->string('firstname', 255)->nullable();
			$table->string('lastname', 255)->nullable();
			$table->string('nationality', 255);
			$table->string('citizenship_number', 255)->nullable();
			$table->string('health_insurance_number', 255)->nullable();
			$table->string('image', 255)->nullable();
			$table->enum('gender', ['male', 'female']);
			$table->date('start_date');
			$table->date('end_date');
			$table->string('end_reason', 255)->nullable();
			$table->date('birth_date');
			$table->integer('default_contact_id')->default(0);
			$table->integer('default_bank_account_id')->default(0);
			$table->tinyInteger('status')->default(0)->nullable();
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_employee_salary', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('employee_salary_id');
			$table->integer('employee_id')->default(0);
			$table->decimal('amount', 19, 2);
			$table->integer('start_year')->default(0);
			$table->integer('start_month')->default(0);
			$table->integer('start_day')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_employee_contact', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('employee_contact_id');
			$table->integer('employee_id')->default(0);
			$table->integer('address_type_id')->default(0);
			$table->string('code', 255);
			$table->string('email', 255)->nullable();
			$table->string('website', 255)->nullable();
			$table->string('telephone_1', 255);
			$table->string('telephone_2', 255);
			$table->string('address_1', 255);
			$table->string('address_2', 255);
			$table->string('country_id', 255);
			$table->string('city_id', 255);
			$table->string('district_id', 255);
			$table->string('postcode', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_employee_bank_account', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('employee_bank_account_id');
			$table->integer('employee_id')->default(0);
			$table->integer('bank_id')->default(0);
			$table->string('currency_code', 255);
			$table->string('code', 255);
			$table->string('type', 255);
			$table->string('name', 255);
			$table->string('owner', 255);
			$table->string('account_number', 255);
			$table->string('branch_code', 255);
			$table->string('iban_number', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_employee_document', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('employee_document_id');
			$table->integer('employee_id')->default(0);
			$table->enum('type', ['legal', 'special']);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->string('file', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_validity', 0)->nullable();
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

		
        Schema::create('acc_order', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Müşteri bilgileri, ödeme/gönderim yöntemleri ve sipariş durumu dahil temel sipariş bilgilerini saklar. Tüm müşteri siparişleri için ana kayıt görevi görür.');

            $table->bigIncrements('order_id')->comment('Sipariş için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->string('code', 50)->unique()->comment('Benzersiz sipariş kodu');
            $table->timestamp('entry_date_time')->useCurrent()->comment('Sipariş kayıt tarih ve zamanı');
            $table->unsignedBigInteger('channel_type_id')->default(0)->comment('Satış kanalına (örn: web, mobil) referans');
            $table->unsignedBigInteger('group_id')->default(0)->comment('Sipariş grubuna referans');
            $table->unsignedBigInteger('operation_id')->default(0)->comment('Operasyona referans');
            $table->unsignedBigInteger('status_id')->default(0)->comment('Sipariş durumuna referans');
            $table->string('language_code', 2)->comment('ISO 639-1 dil kodu (örn: tr, en)');
            $table->string('currency_code', 3)->comment('ISO 4217 para birimi kodu (örn: TRY, USD)');
            $table->string('name', 255)->comment('Sipariş adı veya başlığı');
            $table->string('tag', 100)->nullable()->comment('Kategorizasyon için fatura etiketi');
            $table->text('description')->nullable()->comment('Sipariş açıklaması');
            $table->unsignedBigInteger('consumer_id')->default(0)->comment('Acenta / Tüketici Cari Hesaba referans');
            $table->unsignedBigInteger('customer_id')->default(0)->comment('Müşteri Cari Hesaba referans');
            $table->string('customer_name', 255)->comment('Müşteri Cari Hesap unvanı adı');
            $table->string('customer_tax_office', 100)->comment('Müşteri Cari Hesap Vergi dairesi adı');
            $table->string('customer_tax_number', 50)->comment('Müşteri Cari Hesap Vergi kimlik numarası');
            $table->string('customer_mersis_number', 50)->nullable()->comment('Müşteri Cari Hesap mersis no');
            $table->string('customer_address', 255)->nullable()->comment('Müşteri Cari Hesap fatura adresi');
            $table->string('payment_method', 100)->nullable()->comment('Ödeme yöntemi (örn: credit_card, bank_transfer)');
            $table->string('payment_origin', 100)->nullable()->comment('Ödeme kaynağı (örn: online, mağaza)');
            $table->string('coupon_code', 50)->nullable()->comment('Uygulanan indirim kuponu');
            $table->string('voucher_code', 50)->nullable()->comment('Uygulanan hediye çeki kodu');
            $table->unsignedInteger('reward_point')->nullable()->default(0)->comment('Kullanılan ödül puanı');
            $table->text('comment')->nullable()->comment('Sipariş notları');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index('customer_id', 'idx_order_customer_id');
            $table->index('operation_id', 'idx_order_operation_id');
            $table->index('status_id', 'idx_order_status_id');
            $table->index('payment_method', 'idx_order_payment_method');
            $table->index('created_by', 'idx_order_created_by');
            $table->index('updated_by', 'idx_order_updated_by');
            $table->index('created_at', 'idx_order_created_at');
        });
		
        Schema::create('acc_order_item', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Siparişe ait ürün/hizmet detayları, fiyatlandırma, miktar ve varyant bilgilerini saklar. Her siparişin kalemlerini izler.');

            $table->bigIncrements('order_item_id')->comment('Sipariş kalemi için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->string('code', 50)->unique()->comment('Benzersiz kod');
            $table->unsignedBigInteger('order_id')->comment('Siparişe referans');
            $table->unsignedBigInteger('item_id')->comment('Ürüne/hizmete referans');
            $table->string('item_code', 50)->nullable()->comment('Sipariş kalemi kodu');
            $table->string('item_name', 255)->comment('Referans için sipariş kalemi adı');
            $table->text('item_description')->nullable()->comment('Referans için ürün/hizmet açıklaması');
            $table->decimal('item_price', 15, 2)->comment('Birim fiyat');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000)->comment('Kur dönüşüm oranı');
            $table->string('currency_code', 3)->comment('ISO 4217 para birimi kodu (örn: TRY, USD)');
            $table->unsignedInteger('quantity')->default(1)->comment('Kalem miktarı');
            $table->unsignedBigInteger('unit_id')->comment('Birim tipine referans (örn: adet, kg)');
            $table->unsignedBigInteger('tax_class_id')->comment('Vergi sınıfına referans');
            $table->string('discount_type', 20)->nullable()->comment('İndirim türü (örn: yüzde, sabit)');
            $table->decimal('discount_value', 15, 2)->nullable()->default(0.00)->comment('İndirim tutarı');
            $table->text('options')->nullable()->comment('Kalem seçenekleri (örn: JSON)');
            $table->text('variants')->nullable()->comment('Kalem varyanları (örn: JSON)');
            $table->text('groupeds')->nullable()->comment('Kalem grup ürünleri (örn: JSON)');
            $table->text('bundles')->nullable()->comment('Kalem paket ürünler (örn: JSON)');
			$table->string('delivery_method', 255);
			$table->string('shipping_method', 255);
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index('order_id', 'idx_order_item_order_id');
            $table->index('item_id', 'idx_order_item_item');
            $table->index('unit_id', 'idx_order_item_unit_id');
            $table->index('tax_class_id', 'idx_order_item_tax_class_id');
            $table->index('item_price', 'idx_order_item_item_price');
            $table->index('quantity', 'idx_order_item_quantity');
            $table->index('item_code', 'idx_order_item_item_code');
            $table->index('created_by', 'idx_order_item_created_by');
            $table->index('updated_by', 'idx_order_item_updated_by');
        });
		
        Schema::create('acc_order_financial', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergiler, ücretler, indirimler ve diğer parasal düzenlemeleri yönetir. Tüm finansal kalemlerin hesabını tutar.');

            $table->bigIncrements('order_financial_id')->comment('Sipariş finansal kaydı için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->unsignedBigInteger('order_id')->comment('Siparişe referans');
            $table->string('title', 255)->comment('Finansal kayıt başlığı');
            $table->string('code', 50)->unique()->comment('Benzersiz finansal kayıt kodu');
            $table->string('type', 50)->comment('Finansal tür (örn: vergi, ücret, indirim)');
            $table->unsignedSmallInteger('sort_order')->default(0)->comment('Görüntüleme sıralaması');
            $table->boolean('removable')->default(false)->comment('Kaldırılabilir mi? (false: Hayır, true: Evet)');
            $table->decimal('amount', 15, 2)->comment('Finansal tutar');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index('order_id', 'idx_order_financial_order_id');
            $table->index('amount', 'idx_order_financial_amount');
            $table->index('type', 'idx_order_financial_type');
            $table->index('removable', 'idx_order_financial_removable');
            $table->index('created_by', 'idx_order_financial_created_by');
            $table->index('updated_by', 'idx_order_financial_updated_by');
        });
		
        Schema::create('acc_order_contact', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Siparişler için faturalama ve gönderim amacıyla iletişim ve adres bilgilerini saklar. Müşteri iletişim verilerini yönetir.');

            $table->bigIncrements('order_contact_id')->comment('Sipariş iletişimi için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->unsignedBigInteger('order_id')->comment('Siparişe referans');
            $table->unsignedBigInteger('taxpayer_type_id')->comment('Mükellef türüne referans');
            $table->unsignedBigInteger('address_type_id')->comment('Adres türüne referans');
            $table->string('operation_type', 50)->comment('Operasyon türü (örn: faturalama, gönderim)');
            $table->string('title', 100)->nullable()->comment('İletişim unvanı (örn: Bay, Bayan)');
            $table->string('firstname', 100)->comment('İletişim adı');
            $table->string('lastname', 100)->comment('İletişim soyadı');
            $table->string('citizenship_number', 50)->nullable()->comment('Vatandaşlık numarası (örn: TC Kimlik No)');
            $table->string('company', 255)->nullable()->comment('Şirket adı');
            $table->string('tax_office', 100)->nullable()->comment('Vergi dairesi adı');
            $table->string('tax_number', 50)->nullable()->comment('Vergi kimlik numarası');
            $table->string('email', 255)->nullable()->comment('İletişim e-posta adresi');
            $table->string('telephone', 20)->nullable()->comment('İletişim telefon numarası');
            $table->string('address_1', 255)->comment('Birincil adres satırı');
            $table->string('address_2', 255)->nullable()->comment('İkincil adres satırı');
            $table->string('neighborhood', 100)->nullable()->comment('Mahalle veya semt');
            $table->string('street', 255)->nullable()->comment('Cadde veya sokak adı');
            $table->string('building_info', 100)->nullable()->comment('Bina numarası, daire veya ek bilgi');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->unsignedBigInteger('country_id')->comment('Ülkeye referans');
            $table->unsignedBigInteger('city_id')->comment('Şehre referans');
            $table->unsignedBigInteger('district_id')->comment('İlçeye referans');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index('order_id', 'idx_order_contact_order_id');
            $table->index('taxpayer_type_id', 'idx_order_contact_taxpayer_type_id');
            $table->index('address_type_id', 'idx_order_contact_address_type_id');
            $table->index('country_id', 'idx_order_contact_country_id');
            $table->index('city_id', 'idx_order_contact_city_id');
            $table->index('created_by', 'idx_order_contact_created_by');
            $table->index('updated_by', 'idx_order_contact_updated_by');
            $table->index('operation_type', 'idx_order_contact_operation_type');
            $table->index('tax_number', 'idx_order_contact_tax_number');
        });
		
        Schema::create('acc_order_activity', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Siparişlerin ödeme işlemleri ve bildirimler dahil tüm geçmişini ve durum değişikliklerini takip eder. Tüm sipariş aktiviteleri için denetim izi sağlar.');

            $table->bigIncrements('order_activity_id')->comment('Sipariş aktivitesi için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->unsignedBigInteger('order_id')->comment('Siparişe referans');
            $table->unsignedBigInteger('operation_id')->comment('Operasyona referans');
            $table->unsignedBigInteger('status_id')->comment('Sipariş durumuna referans');
            $table->boolean('notified')->default(false)->comment('Bildirim gönderildi mi? (false: Hayır, true: Evet)');
            $table->string('comment', 255)->nullable()->comment('Aktivite yorumu');
            $table->text('payment_operation')->nullable()->comment('Ödeme işlemi detayları (örn: JSON)');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index('order_id', 'idx_order_activity_order_id');
            $table->index('operation_id', 'idx_order_activity_operation');
            $table->index('status_id', 'idx_order_activity_status_id');
            $table->index('notified', 'idx_order_activity_notified');
            $table->index('created_by', 'idx_order_activity_created_by');
            $table->index('updated_by', 'idx_order_activity_updated_by');
            $table->index('created_at', 'idx_order_activity_created_at');
        });
		
        Schema::create('acc_order_shipment', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('order_shipment_id');
			$table->integer('order_id')->default(0);
			$table->string('code', 255);
			$table->integer('cargo_id')->default(0);
			$table->string('tracking_code', 255);
			$table->timestamp('date_shipment', 0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_order_shipment_item', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('order_shipment_item_id');
			$table->integer('order_id')->default(0);
			$table->integer('order_shipment_id')->default(0);
			$table->integer('order_item_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_invoice', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Hesap bilgileri, ödeme koşulları ve durum takibi dahil temel fatura bilgilerini saklar. Oluşturmadan ödemeye kadar fatura yaşam döngüsünü yönetir.');

            $table->bigIncrements('invoice_id')->comment('Fatura için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->string('code', 50)->unique()->comment('Benzersiz fatura kodu');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Fatura türüne referans');
            $table->unsignedBigInteger('group_id')->default(0)->comment('Fatura grubuna referans');
            $table->unsignedBigInteger('category_id')->nullable()->default(0)->comment('Fatura kategorisine referans');
            $table->unsignedBigInteger('status_id')->default(0)->comment('Fatura durumuna referans');
            $table->unsignedBigInteger('order_id')->nullable()->default(0)->comment('Siparişe referans');
            $table->string('currency_code', 3)->comment('ISO 4217 para birimi kodu (örn: TRY, USD)');
            $table->string('language_code', 2)->nullable()->comment('ISO 639-1 dil kodu (örn: tr, en)');
            $table->string('name', 255)->comment('Fatura adı veya başlığı');
            $table->string('tag', 100)->nullable()->comment('Kategorizasyon için fatura etiketi');
            $table->text('description')->nullable()->comment('Fatura açıklaması');
            $table->timestamp('entry_date_time')->useCurrent()->comment('Fatura kayıt tarih ve zamanı');
            $table->string('entry_serial', 50)->comment('Fatura seri numarası');
            $table->string('entry_queue', 50)->comment('Fatura kuyruk tanımlayıcısı');
            $table->unsignedBigInteger('customer_id')->default(0)->comment('Müşteri Cari Hesaba referans');
            $table->string('customer_name', 255)->comment('Müşteri Cari Referans için hesap adı');
            $table->string('customer_tax_office', 100)->comment('Müşteri Cari Hesap Vergi dairesi adı');
            $table->string('customer_tax_number', 50)->comment('Müşteri Cari Hesap Vergi kimlik numarası');
            $table->string('customer_mersis_number', 50)->comment('Müşteri Cari Hesap mersis no');
            $table->string('customer_address', 255)->nullable()->comment('Müşteri Cari Hesap fatura adresi');
            $table->timestamp('payment_due_date')->nullable()->comment('Ödeme vade tarihi');
            $table->tinyInteger('waybill_status')->nullable()->default(0)->comment('İrsaliye durumu (0: Yok, 1: Düzenlendi)');
            $table->timestamp('waybill_date')->nullable()->comment('İrsaliye düzenleme tarihi');
            $table->string('waybill_number', 50)->nullable()->comment('İrsaliye numarası');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index(['customer_id', 'payment_due_date'], 'idx_invoice_customer_due');
            $table->index('status_id', 'idx_invoice_status_id');
            $table->index('type_id', 'idx_invoice_type_id');
            $table->index('category_id', 'idx_invoice_category_id');
            $table->index('order_id', 'idx_invoice_order_id');
            $table->index('entry_date_time', 'idx_invoice_entry_date_time');
            $table->index('created_by', 'idx_invoice_created_by');
            $table->index('updated_by', 'idx_invoice_updated_by');
        });
		
        Schema::create('acc_invoice_item', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Detaylı ürün/hizmet bilgileri, fiyatlandırma, vergiler ve indirimler içeren fatura kalemlerini saklar. Fatura toplamını oluşturan bireysel bileşenleri izler.');

            $table->bigIncrements('invoice_item_id')->comment('Fatura kalemi için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->string('code', 50)->comment('Benzersiz kod');
            $table->unsignedBigInteger('invoice_id')->comment('Faturaya referans');
            $table->unsignedBigInteger('item_id')->comment('Ürüne/hizmete referans');
            $table->string('item_code', 50)->nullable()->comment('Benzersiz kod');
            $table->string('item_name', 255)->comment('Referans için ürün/hizmet adı');
            $table->text('item_description')->nullable()->comment('Referans için ürün/hizmet açıklaması');
            $table->decimal('item_price', 15, 2)->comment('Birim fiyat');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000)->comment('Para birimi dönüşüm kuru');
            $table->string('currency_code', 3)->comment('ISO 4217 para birimi kodu (örn: TRY, USD)');
            $table->unsignedInteger('quantity')->default(1)->comment('Kalem miktarı');
            $table->unsignedBigInteger('unit_id')->comment('Birim tipine referans (örn: adet, kg)');
            $table->unsignedBigInteger('tax_class_id')->comment('Vergi sınıfına referans');
            $table->string('discount_type', 20)->nullable()->comment('İndirim türü (örn: yüzde, sabit)');
            $table->decimal('discount_value', 15, 2)->nullable()->default(0.00)->comment('İndirim tutarı');
            $table->string('special_consumption_tax_type', 20)->nullable()->comment('Özel tüketim vergisi türü');
            $table->unsignedTinyInteger('special_consumption_tax_rate')->nullable()->default(0)->comment('Özel tüketim vergi oranı');
            $table->string('special_communication_tax_type', 20)->nullable()->comment('Özel iletişim vergisi türü');
            $table->unsignedTinyInteger('special_communication_tax_rate')->nullable()->default(0)->comment('Özel iletişim vergi oranı');
			$table->string('delivery_method', 255);
			$table->string('shipping_method', 255);
			$table->string('variant', 255);
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index('invoice_id', 'idx_invoice_item_invoice_id');
            $table->index('item_id', 'idx_invoice_item_item');
            $table->index('unit_id', 'idx_invoice_item_unit_id');
            $table->index('tax_class_id', 'idx_invoice_item_tax_class_id');
            $table->index('quantity', 'idx_invoice_item_quantity');
            $table->index('created_by', 'idx_invoice_item_created_by');
            $table->index('updated_by', 'idx_invoice_item_updated_by');
        });
		
        Schema::create('acc_invoice_financial', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Vergiler, ücretler ve ek masraflar gibi faturaya uygulanan finansal düzenlemeleri yönetir. Fatura toplamını etkileyen tüm parasal bileşenleri hesaplar ve izler.');

            $table->bigIncrements('invoice_financial_id')->comment('Fatura finansal kaydı için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->unsignedBigInteger('invoice_id')->comment('Faturaya referans');
            $table->string('title', 255)->comment('Finansal kalemin başlığı (örn: Ara Toplam, KDV, Genel Toplam)');
            $table->string('code', 50)->comment('Finansal kalemin kodu (örn: SUBTOTAL, VAT, DISCOUNT)');
            $table->string('type', 50)->comment('Finansal kalemin türü (örn: total, tax, discount, other)');
            $table->unsignedSmallInteger('sort_order')->default(0)->comment('Görüntüleme sıralama sırası');
            $table->decimal('amount', 15, 2)->comment('Finansal kalemin tutarı');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index('invoice_id', 'idx_invoice_financial_invoice_id');
            $table->index('created_by', 'idx_invoice_financial_created_by');
            $table->index('updated_by', 'idx_invoice_financial_updated_by');
            $table->index('amount', 'idx_invoice_financial_amount');
            $table->index('type', 'idx_invoice_financial_type');
            $table->index('code', 'idx_invoice_financial_code');
        });
		
        Schema::create('acc_invoice_contact', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Fatura için faturalama ve gönderim amacıyla ilgili iletişim ve adres bilgilerini saklar. Farklı operasyonel ihtiyaçlar için ayrı iletişim kayıtları tutar.');

            $table->bigIncrements('invoice_contact_id')->comment('Fatura iletişimi için birincil anahtar');
            $table->uuid('uuid')->unique()->comment('Sistem tarafından oluşturulan benzersiz talep UUID');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->unsignedBigInteger('invoice_id')->comment('Faturaya referans');
            $table->string('operation_type', 50)->comment('Operasyon türü (örn: faturalama, gönderim)');
            $table->unsignedBigInteger('address_type_id')->comment('Adres türüne referans');
            $table->string('title', 100)->nullable()->comment('İletişim unvanı (örn: Bay, Bayan)');
            $table->string('firstname', 100)->comment('İletişim adı');
            $table->string('lastname', 100)->comment('İletişim soyadı');
            $table->string('company', 255)->nullable()->comment('Şirket adı');
            $table->string('tax_office', 100)->comment('Vergi dairesi adı');
            $table->string('tax_number', 50)->comment('Vergi kimlik numarası');
            $table->string('email', 255)->nullable()->comment('İletişim e-posta adresi');
            $table->string('telephone', 20)->nullable()->comment('İletişim telefon numarası');
            $table->string('address_1', 255)->comment('Birincil adres satırı');
            $table->string('address_2', 255)->nullable()->comment('İkincil adres satırı');
            $table->string('neighborhood', 100)->nullable()->comment('Mahalle veya semt');
            $table->string('street', 255)->nullable()->comment('Cadde veya sokak adı');
            $table->string('building_info', 100)->nullable()->comment('Bina numarası, daire veya ek bilgi');
            $table->unsignedBigInteger('country_id')->comment('Ülkeye referans');
            $table->unsignedBigInteger('city_id')->comment('Şehre referans');
            $table->unsignedBigInteger('district_id')->comment('İlçeye referans');
            $table->string('postcode', 20)->nullable()->comment('Posta kodu');
            $table->timestamp('created_at')->useCurrent()->comment('Oluşturma zamanı');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturma kullanıcı ID');
            $table->string('created_ip_address', 45)->nullable()->comment('Oluşturma IP adresi');
            $table->string('created_user_agent', 255)->nullable()->comment('Oluşturma cihaz/uygulama bilgisi');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleme kullanıcı ID');
            $table->string('updated_ip_address', 45)->nullable()->comment('Güncelleme IP adresi');
            $table->string('updated_user_agent', 255)->nullable()->comment('Güncelleme cihaz/uygulama bilgisi');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Güncelleme zamanı');

            $table->index('invoice_id', 'idx_invoice_contact_invoice_id');
            $table->index('address_type_id', 'idx_invoice_contact_address_type_id');
            $table->index('country_id', 'idx_invoice_contact_country_id');
            $table->index('city_id', 'idx_invoice_contact_city_id');
            $table->index('created_by', 'idx_invoice_contact_created_by');
            $table->index('updated_by', 'idx_invoice_contact_updated_by');
            $table->index('operation_type', 'idx_invoice_contact_operation_type');
        });
		
        Schema::create('acc_refund', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8mb4';
			$table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('refund_id');
			$table->string('code', 255);
			$table->integer('account_id')->default(0);
			$table->integer('order_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->integer('status_id')->default(0);
			$table->integer('reason_id')->default(0);
			$table->integer('action_id')->default(0);
			$table->integer('opened')->default(0);
			$table->string('product_name', 255);
			$table->decimal('product_price', 19, 2);
			$table->decimal('exchange_rate', 19, 2);
			$table->string('currency_code', 255);
			$table->integer('quantity')->default(0);
			$table->integer('unit_id')->default(0);
			$table->integer('tax_class_id')->default(0);
			$table->string('discount_type', 255);
			$table->decimal('discount_value', 19, 2);
			$table->longText('comment');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('acc_recurring', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8mb4';
			$table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('recurring_id');
			$table->string('code', 255);
			$table->integer('recurring_type_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->integer('quantity')->default(0);
			$table->decimal('product_price', 19, 2);
			$table->string('currency_code', 255);
			$table->integer('tax_class_id')->default(0);
			$table->datetime("date_start")->nullable();
			$table->datetime("date_end")->nullable();
			$table->string('user_agent', 255);
			$table->string('accept_language', 255);
			$table->string('ip_created', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('acc_cash');
        Schema::dropIfExists('acc_cash_bank');
        Schema::dropIfExists('acc_cash_activity');
		
        Schema::dropIfExists('acc_check');
		
        Schema::dropIfExists('acc_loan');
		
        Schema::dropIfExists('acc_demand');
		
        Schema::dropIfExists('acc_employee');
        Schema::dropIfExists('acc_employee_contact');
        Schema::dropIfExists('acc_employee_document');
        Schema::dropIfExists('acc_employee_bank_account');
		
        Schema::dropIfExists('acc_order');
        Schema::dropIfExists('acc_order_item');
        Schema::dropIfExists('acc_order_contact');
        Schema::dropIfExists('acc_order_financial');
        Schema::dropIfExists('acc_order_shipment');
        Schema::dropIfExists('acc_order_shipment_item');
		
        Schema::dropIfExists('acc_invoice');
        Schema::dropIfExists('acc_invoice_item');
        Schema::dropIfExists('acc_invoice_financial');
        Schema::dropIfExists('acc_invoice_contact');
		
        Schema::dropIfExists('acc_refund');
		
        Schema::dropIfExists('acc_recurring');
    }

};
