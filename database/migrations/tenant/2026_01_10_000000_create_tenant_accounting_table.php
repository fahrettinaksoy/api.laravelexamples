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

    private const TABLE_CASH = 'acc_cash';

    private const TABLE_CASH_TRANSACTION = 'acc_cash_transaction';

    private const TABLE_CHECK = 'acc_check';

    private const TABLE_LOAN = 'acc_loan';

    private const TABLE_EMPLOYEE = 'acc_employee';

    private const TABLE_EMPLOYEE_SALARY = 'acc_employee_salary';

    private const TABLE_EMPLOYEE_CONTACT = 'acc_employee_contact';

    private const TABLE_EMPLOYEE_BANK_ACCOUNT = 'acc_employee_bank_account';

    private const TABLE_EMPLOYEE_DOCUMENT = 'acc_employee_document';

    private const TABLE_ORDER = 'acc_order';

    private const TABLE_ORDER_ITEM = 'acc_order_item';

    private const TABLE_ORDER_FINANCIAL = 'acc_order_financial';

    private const TABLE_ORDER_CONTACT = 'acc_order_contact';

    private const TABLE_ORDER_ACTIVITY = 'acc_order_activity';

    private const TABLE_ORDER_SHIPMENT = 'acc_order_shipment';

    private const TABLE_ORDER_SHIPMENT_ITEM = 'acc_order_shipment_item';

    private const TABLE_INVOICE = 'acc_invoice';

    private const TABLE_INVOICE_ITEM = 'acc_invoice_item';

    private const TABLE_INVOICE_FINANCIAL = 'acc_invoice_financial';

    private const TABLE_INVOICE_CONTACT = 'acc_invoice_contact';

    private const TABLE_REFUND = 'acc_refund';

    private const TABLE_DEMAND = 'acc_demand';

    private const TABLE_RECURRING = 'acc_recurring';

    public function up(): void
    {
        Schema::create(self::TABLE_CASH, function (Blueprint $table) {
            $table->comment('Nakit kasa ve banka hesap tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('cash_id')->comment('Kasa benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('type_id')->default(0)->comment('Kasa türü (Nakit, Banka vb.)');
            $table->string('code', 100)->unique()->comment('Kasa kodu');
            $table->string('name', 255)->comment('Kasa adı');
            $table->string('description', 500)->nullable()->comment('Açıklama');
            $table->char('currency_code', 3)->default('TRY')->comment('Para birimi');
            $table->unsignedBigInteger('bank_id')->default(0)->comment('Banka tanım ID');
            $table->string('account_name', 255)->nullable()->comment('Banka hesap adı');
            $table->string('account_owner', 255)->nullable()->comment('Hesap sahibi');
            $table->string('account_number', 50)->nullable()->comment('Hesap numarası');
            $table->string('branch_code', 50)->nullable()->comment('Şube kodu');
            $table->string('iban', 50)->nullable()->comment('IBAN numarası');
            $table->string('swift', 50)->nullable()->comment('Swift kodu');
            $table->decimal('daily_limit', 19, 4)->default(0)->comment('Günlük işlem limiti');
            $table->decimal('min_balance', 19, 4)->default(0)->comment('Minimum bakiye uyarısı');
            $table->decimal('max_balance', 19, 4)->default(0)->comment('Maksimum bakiye uyarısı');
            $table->boolean('is_default')->default(false)->comment('Varsayılan kasa mı?');
            $table->boolean('status')->default(true)->index()->comment('Durum (Aktif/Pasif)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Silinme tarihi (Soft Delete)');

            $table->index('deleted_at', 'idx_cash_soft_delete');
        });

        Schema::create(self::TABLE_CASH_TRANSACTION, function (Blueprint $table) {
            $table->comment('Kasa hareketleri ve finansal işlem logları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('cash_transaction_id')->comment('Hareket benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('code', 100)->unique()->comment('İşlem kodu');
            $table->unsignedBigInteger('cash_id')->index()->comment('İlgili kasa ID');
            $table->unsignedBigInteger('type_id')->comment('İşlem tipi (Giriş/Çıkış vb.)');
            $table->unsignedBigInteger('flow_id')->default(0)->comment('Akış tipi (Tahsilat, Tediye)');
            $table->unsignedBigInteger('account_id')->default(0)->index()->comment('İlgili cari hesap ID');
            $table->unsignedBigInteger('related_id')->default(0)->index()->comment('İlgili modül ID (Sipariş, Fatura)');
            $table->string('related_model', 100)->nullable()->comment('İlgili modül modeli (Invoice, Order)');
            $table->decimal('amount', 19, 4)->comment('İşlem tutarı');
            $table->char('currency_code', 3)->default('TRY');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->string('title', 255)->nullable()->comment('İşlem başlığı');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->date('date_activity')->comment('İşlem tarihi');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['cash_id', 'date_activity'], 'idx_cash_trans_date');
            $table->index('deleted_at', 'idx_cash_trans_soft_delete');
        });

        Schema::create(self::TABLE_CHECK, function (Blueprint $table) {
            $table->comment('Çek ve senet takibi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('check_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('account_id')->default(0)->index()->comment('Keşideci/Müşteri ID');
            $table->unsignedBigInteger('bank_id')->default(0)->comment('Banka ID');
            $table->string('bank_name', 255)->nullable();
            $table->string('check_number', 50)->comment('Çek numarası');
            $table->unsignedInteger('type_id')->default(0)->comment('Çek türü (Müşteri/Kendi)');
            $table->decimal('amount', 19, 4);
            $table->char('currency_code', 3)->default('TRY');
            $table->date('date_issue')->nullable()->comment('Düzenleme tarihi');
            $table->date('date_due')->nullable()->comment('Vade tarihi');
            $table->string('owner_name', 255)->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment('Çek durumu');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_check_soft_delete');
        });

        Schema::create(self::TABLE_LOAN, function (Blueprint $table) {
            $table->comment('Kredi ve borç takibi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('loan_id');
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('type_id')->default(0);
            $table->unsignedBigInteger('account_id')->default(0)->index();
            $table->string('code', 100)->unique();
            $table->decimal('amount', 19, 4);
            $table->char('currency_code', 3)->default('TRY');
            $table->date('date_due')->nullable();
            $table->unsignedTinyInteger('status')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_loan_soft_delete');
        });

        Schema::create(self::TABLE_EMPLOYEE, function (Blueprint $table) {
            $table->comment('Personel ve çalışan kartları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('employee_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('department_id')->nullable()->index();
            $table->string('code', 100)->unique();
            $table->string('title', 100)->nullable();
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('citizenship_id', 20)->nullable()->comment('TCKN/Pasaport No');
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_start')->nullable()->comment('İşe başlama');
            $table->date('date_end')->nullable()->comment('İşten ayrılma');
            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_employee_soft_delete');
        });

        Schema::create(self::TABLE_EMPLOYEE_SALARY, function (Blueprint $table) {
            $table->comment('Personel maaş ve ödeme geçmişi');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('employee_salary_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('employee_id')->index();
            $table->decimal('amount', 19, 4);
            $table->date('period')->comment('Maaş dönemi');
            $table->boolean('is_paid')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_emp_salary_soft_delete');
        });

        Schema::create(self::TABLE_EMPLOYEE_CONTACT, function (Blueprint $table) {
            $table->comment('Personel iletişim bilgileri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('employee_contact_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('address_1', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('zip_code', 20)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_emp_contact_soft_delete');
        });

        Schema::create(self::TABLE_EMPLOYEE_BANK_ACCOUNT, function (Blueprint $table) {
            $table->comment('Personel banka hesapları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('employee_bank_account_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('bank_name', 100);
            $table->string('iban', 50)->nullable();
            $table->string('account_number', 50)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create(self::TABLE_EMPLOYEE_BANK_ACCOUNT, function (Blueprint $table) {
            $table->comment('Personel banka hesapları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('employee_bank_account_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('bank_name', 100);
            $table->string('iban', 50)->nullable();
            $table->string('account_number', 50)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_emp_bank_soft_delete');
        });

        Schema::create(self::TABLE_EMPLOYEE_DOCUMENT, function (Blueprint $table) {
            $table->comment('Personel evrakları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('employee_document_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('name', 255);
            $table->string('file', 500);
            $table->string('type', 50)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_emp_doc_soft_delete');
        });

        Schema::create(self::TABLE_ORDER, function (Blueprint $table) {
            $table->comment('Sipariş ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('order_id');
            $table->uuid('uuid')->unique();
            $table->string('code', 50)->unique()->comment('Sipariş No');
            $table->unsignedBigInteger('customer_id')->default(0)->index()->comment('Müşteri (Cari) ID');
            $table->unsignedBigInteger('status_id')->default(1)->index()->comment('Sipariş durumu');
            $table->string('customer_name', 255)->comment('Sipariş anındaki müşteri adı');
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->decimal('total', 19, 4)->default(0);
            $table->decimal('tax_total', 19, 4)->default(0);
            $table->decimal('discount_total', 19, 4)->default(0);
            $table->char('currency_code', 3)->default('TRY');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->string('payment_method', 100)->nullable();
            $table->string('shipping_method', 100)->nullable();
            $table->text('comment')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_order_soft_delete');
        });

        Schema::create(self::TABLE_ORDER_ITEM, function (Blueprint $table) {
            $table->comment('Sipariş kalemleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('order_item_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->default(0)->index();
            $table->string('name', 255)->comment('Ürün adı (Anlık)');
            $table->string('sku', 100)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 19, 4)->comment('Birim fiyat');
            $table->decimal('tax', 19, 4)->default(0);
            $table->decimal('discount', 19, 4)->default(0);
            $table->decimal('total', 19, 4)->comment('Satır toplamı');
            $table->json('options')->nullable()->comment('Seçili opsiyonlar');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_order_item_soft_delete');
        });

        Schema::create(self::TABLE_ORDER_FINANCIAL, function (Blueprint $table) {
            $table->comment('Sipariş ek finansal kalemler (Kargo ücreti, Ek vergi vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('order_financial_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('title', 255);
            $table->string('code', 50)->comment('shipping, tax, discount');
            $table->decimal('amount', 19, 4);
            $table->integer('sort_order')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_order_fin_soft_delete');
        });

        Schema::create(self::TABLE_ORDER_CONTACT, function (Blueprint $table) {
            $table->comment('Sipariş teslimat ve fatura adresleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('order_contact_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id')->index();
            $table->enum('type', ['shipping', 'billing'])->comment('Adres tipi');
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('address_1', 255);
            $table->string('city', 100);
            $table->string('country', 100);
            $table->string('postcode', 20)->nullable();
            $table->string('phone', 50)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_order_contact_soft_delete');
        });

        Schema::create(self::TABLE_ORDER_ACTIVITY, function (Blueprint $table) {
            $table->comment('Sipariş geçmişi ve logları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('order_activity_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->string('comment', 500)->nullable();
            $table->boolean('is_customer_notified')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_order_act_soft_delete');
        });

        Schema::create(self::TABLE_ORDER_SHIPMENT, function (Blueprint $table) {
            $table->comment('Sipariş gönderim/kargo kayıtları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('order_shipment_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('tracking_number', 100)->nullable();
            $table->string('carrier_name', 100)->nullable();
            $table->string('tracking_url', 500)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_order_ship_soft_delete');
        });

        Schema::create(self::TABLE_ORDER_SHIPMENT_ITEM, function (Blueprint $table) {
            $table->comment('Gönderime dahil edilen ürünler');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('order_shipment_item_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_shipment_id')->index();
            $table->unsignedBigInteger('order_item_id');
            $table->integer('quantity')->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_order_ship_item_soft_delete');
        });

        Schema::create(self::TABLE_INVOICE, function (Blueprint $table) {
            $table->comment('Resmi fatura ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('invoice_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id')->default(0)->index()->comment('Bağlı sipariş ID');
            $table->string('invoice_no', 50)->unique()->nullable();
            $table->unsignedBigInteger('customer_id')->index();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('tax_office', 100)->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->decimal('subtotal', 19, 4);
            $table->decimal('tax_total', 19, 4);
            $table->decimal('total', 19, 4);
            $table->char('currency_code', 3)->default('TRY');
            $table->unsignedTinyInteger('status')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_inv_soft_delete');
        });

        Schema::create(self::TABLE_INVOICE_ITEM, function (Blueprint $table) {
            $table->comment('Fatura kalemleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('invoice_item_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->string('name', 255);
            $table->integer('quantity');
            $table->decimal('price', 19, 4);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 19, 4);
            $table->decimal('total', 19, 4);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_inv_item_soft_delete');
        });

        Schema::create(self::TABLE_INVOICE_FINANCIAL, function (Blueprint $table) {
            $table->comment('Fatura alt toplamlar ve ek vergiler');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('invoice_financial_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->string('title', 255);
            $table->string('code', 50);
            $table->decimal('amount', 19, 4);
            $table->integer('sort_order')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_inv_fin_soft_delete');
        });

        Schema::create(self::TABLE_INVOICE_CONTACT, function (Blueprint $table) {
            $table->comment('Fatura üzerindeki adres bilgileri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('invoice_contact_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->string('name', 255);
            $table->text('address');
            $table->string('city', 100);
            $table->string('country', 100);
            $table->string('tax_office', 100)->nullable();
            $table->string('tax_number', 50)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_inv_contact_soft_delete');
        });

        Schema::create(self::TABLE_REFUND, function (Blueprint $table) {
            $table->comment('Ürün iade talepleri');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('refund_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('order_item_id')->default(0);
            $table->unsignedBigInteger('customer_id')->index();
            $table->string('reason', 255)->comment('İade sebebi');
            $table->unsignedTinyInteger('status')->default(0)->comment('Talep durumu');
            $table->integer('quantity')->default(1);
            $table->text('comment')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_refund_soft_delete');
        });

        Schema::create(self::TABLE_DEMAND, function (Blueprint $table) {
            $table->comment('Genel kullanıcı talepleri formu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('demand_id');
            $table->uuid('uuid')->unique();
            $table->string('subject', 255)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('email', 255);
            $table->string('name', 255);
            $table->text('message');
            $table->unsignedTinyInteger('status')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_demand_soft_delete');
        });

        Schema::create(self::TABLE_RECURRING, function (Blueprint $table) {
            $table->comment('Düzenli tekrarlayan işlemler (Abonelikler)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('recurring_id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('product_id')->default(0);
            $table->string('frequency', 50)->comment('day, week, month, year');
            $table->decimal('price', 19, 4);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('deleted_at', 'idx_recurring_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_RECURRING);
        Schema::dropIfExists(self::TABLE_DEMAND);
        Schema::dropIfExists(self::TABLE_REFUND);
        Schema::dropIfExists(self::TABLE_INVOICE_CONTACT);
        Schema::dropIfExists(self::TABLE_INVOICE_FINANCIAL);
        Schema::dropIfExists(self::TABLE_INVOICE_ITEM);
        Schema::dropIfExists(self::TABLE_INVOICE);
        Schema::dropIfExists(self::TABLE_ORDER_SHIPMENT_ITEM);
        Schema::dropIfExists(self::TABLE_ORDER_SHIPMENT);
        Schema::dropIfExists(self::TABLE_ORDER_ACTIVITY);
        Schema::dropIfExists(self::TABLE_ORDER_CONTACT);
        Schema::dropIfExists(self::TABLE_ORDER_FINANCIAL);
        Schema::dropIfExists(self::TABLE_ORDER_ITEM);
        Schema::dropIfExists(self::TABLE_ORDER);
        Schema::dropIfExists(self::TABLE_EMPLOYEE_DOCUMENT);
        Schema::dropIfExists(self::TABLE_EMPLOYEE_BANK_ACCOUNT);
        Schema::dropIfExists(self::TABLE_EMPLOYEE_CONTACT);
        Schema::dropIfExists(self::TABLE_EMPLOYEE_SALARY);
        Schema::dropIfExists(self::TABLE_EMPLOYEE);
        Schema::dropIfExists(self::TABLE_LOAN);
        Schema::dropIfExists(self::TABLE_CHECK);
        Schema::dropIfExists(self::TABLE_CASH_TRANSACTION);
        Schema::dropIfExists(self::TABLE_CASH);
    }
};
