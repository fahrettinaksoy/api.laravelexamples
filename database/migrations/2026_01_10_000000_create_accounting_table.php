<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	
        Schema::create('accntng_safe', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('safe_id');
			$table->integer('type_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->string('currency_code', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_safe_bank', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('safe_bank_id');
			$table->integer('safe_id')->default(0);
			$table->integer('bank_id')->default(0);
			$table->string('type', 255);
			$table->string('name', 255);
			$table->string('owner', 255);
			$table->string('account_number', 255);
			$table->string('branch_code', 255);
			$table->string('iban_number', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_safe_activity', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('safe_activity_id');
			$table->integer('safe_id')->default(0);
			$table->integer('process_id')->default(0);
			$table->integer('module_group_id')->default(0);
			$table->integer('module_type_id')->default(0);
			$table->integer('module_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->integer('branch_id')->default(0);
			$table->decimal('amount', 19, 2);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->text('content');
			$table->timestamp('date_activity', 0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
	
        Schema::create('accntng_loan', function (Blueprint $table) {
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
		
        Schema::create('accntng_check', function (Blueprint $table) {
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
		
        Schema::create('accntng_personnel', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('personnel_id');
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
		
        Schema::create('accntng_personnel_salary', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('personnel_salary_id');
			$table->integer('personnel_id')->default(0);
			$table->decimal('amount', 19, 2);
			$table->integer('start_year')->default(0);
			$table->integer('start_month')->default(0);
			$table->integer('start_day')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_personnel_contact', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('personnel_contact_id');
			$table->integer('personnel_id')->default(0);
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
		
        Schema::create('accntng_personnel_bank_account', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('personnel_bank_account_id');
			$table->integer('personnel_id')->default(0);
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
		
        Schema::create('accntng_personnel_document', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('personnel_document_id');
			$table->integer('personnel_id')->default(0);
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
		
        Schema::create('accntng_cart', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('cart_id');
			$table->string('code', 255);
			$table->string('session', 255);
			$table->integer('account_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->integer('recurring_type_id')->default(0);
			$table->integer('variant_stock_id')->default(0);
			$table->integer('quantity')->default(0);
			$table->longText('options');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_order', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('order_id');
			$table->string('code', 255);
			$table->string('group_code', 255);
			$table->string('channel_code', 255);
			$table->integer('account_id')->default(0);
			$table->string('account_name', 255);
			$table->integer('account_group_id')->default(0);
			$table->integer('operation_id')->default(0);
			$table->integer('status_id')->default(0);
			$table->string('language_code', 255);
			$table->string('currency_code', 255);
			$table->string('payment_method', 255);
			$table->integer('payment_contact_id')->default(0);
			$table->string('payment_origin', 255);
			$table->string('shipping_method', 255);
			$table->integer('shipping_contact_id')->default(0);
			$table->string('coupon_code', 255);
			$table->string('voucher_code', 255);
			$table->string('reward_point', 255);
			$table->longText('comment');
			$table->integer('recurring_id')->default(0);
			$table->string('user_agent', 255);
			$table->string('accept_language', 255);
			$table->string('ip_modified', 255);
			$table->string('ip_created', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_order_item', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('order_item_id');
			$table->integer('order_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->integer('recurring_id')->default(0);
			$table->integer('variant_stock_id')->default(0);
			$table->string('product_code', 255);
			$table->string('product_name', 255);
			$table->decimal('product_price', 19, 2);
			$table->decimal('exchange_rate', 19, 2);
			$table->string('currency_code', 255);
			$table->integer('quantity')->default(0);
			$table->integer('unit_id')->default(0);
			$table->integer('tax_class_id')->default(0);
			$table->string('discount_type', 255);
			$table->decimal('discount_value', 19, 2);
			$table->string('excise_duty_type', 255);
			$table->integer('excise_duty_rate')->default(0);
			$table->integer('communications_tax_rate')->default(0);
			$table->string('delivery_method', 255);
			$table->string('shipping_method', 255);
			$table->longText('options');
			$table->text('variants');
			$table->text('groupeds');
			$table->text('bundles');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_order_financial', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('order_financial_id');
			$table->integer('order_id')->default(0);
			$table->string('title', 255);
			$table->string('code', 255);
			$table->string('type', 255);
			$table->integer('sort_order')->default(0);
			$table->integer('removable')->default(0);
			$table->decimal('value', 19, 2);
			$table->string('amount', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_order_contact', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('order_contact_id');
			$table->integer('order_id')->default(0);
			$table->integer('taxpayer_type_id')->default(0);
			$table->string('operation_type', 255);
			$table->integer('address_type_id')->default(0);
			$table->string('title', 255);
			$table->string('firstname', 255);
			$table->string('lastname', 255);
			$table->string('citizenship_number', 255);
			$table->string('company', 255);
			$table->string('tax_office', 255);
			$table->string('tax_number', 255);
			$table->string('email', 255);
			$table->string('telephone', 255);
			$table->string('address_1', 255);
			$table->string('address_2', 255);
			$table->string('country_id', 255);
			$table->string('city_id', 255);
			$table->string('district_id', 255);
			$table->string('postcode', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_order_activity', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('order_activity_id');
			$table->integer('order_id')->default(0);
			$table->integer('operation_id')->default(0);
			$table->integer('status_id')->default(0);
			$table->integer('notification')->default(0);
			$table->string('comment', 255);
			$table->longText('payment_operation');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_order_shipment', function (Blueprint $table) {
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
		
        Schema::create('accntng_order_shipment_item', function (Blueprint $table) {
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
		
        Schema::create('accntng_invoice', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
			$table->bigIncrements('invoice_id');
			$table->integer('type_id')->default(0);
			$table->string('group_code', 255);
			$table->string('currency_code', 255);
			$table->string('language_code', 255);
			$table->string('code', 255);
			$table->string('title', 255);
			$table->integer('category_id')->default(0);
			$table->string('tag', 255);
			$table->longText('description');
			$table->string('entry_date_time', 255);
			$table->string('entry_serial', 255);
			$table->string('entry_queue', 255);
			$table->integer('account_id')->default(0);
			$table->string('account_name', 255);
			$table->integer('account_group_id')->default(0);
			$table->integer('account_contact_id')->default(0);
			$table->tinyInteger('account_abroad')->default(0);
			$table->string('tax_office', 255);
			$table->string('tax_number', 255);
			$table->datetime("payment_due_date")->nullable();
			$table->integer('waybill_status')->default(0);
			$table->datetime("waybill_date")->nullable();
			$table->string('waybill_number', 255);
			$table->integer('order_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_invoice_item', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('invoice_item_id');
			$table->integer('invoice_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->string('product_name', 255);
			$table->decimal('product_price', 19, 2);
			$table->decimal('exchange_rate', 19, 2);
			$table->string('currency_code', 255);
			$table->integer('quantity')->default(0);
			$table->integer('unit_id')->default(0);
			$table->integer('tax_class_id')->default(0);
			$table->string('discount_type', 255);
			$table->decimal('discount_value', 19, 2);
			$table->string('excise_duty_type', 255);
			$table->integer('excise_duty_rate')->default(0);
			$table->integer('communications_tax_rate')->default(0);
			$table->string('delivery_method', 255);
			$table->string('shipping_method', 255);
			$table->string('variant', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_invoice_financial', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('invoice_financial_id');
			$table->integer('invoice_id')->default(0);
			$table->string('title', 255);
			$table->string('code', 255);
			$table->string('type', 255);
			$table->integer('sort_order')->default(0);
			$table->decimal('amount', 19, 2);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_invoice_contact', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('invoice_contact_id');
			$table->integer('invoice_id')->default(0);
			$table->string('operation_type', 255);
			$table->integer('address_type_id')->default(0);
			$table->string('title', 255);
			$table->string('firstname', 255);
			$table->string('lastname', 255);
			$table->string('company', 255);
			$table->string('tax_office', 255);
			$table->string('tax_number', 255);
			$table->string('email', 255);
			$table->string('telephone', 255);
			$table->string('address_1', 255);
			$table->string('address_2', 255);
			$table->string('country_id', 255);
			$table->string('city_id', 255);
			$table->string('district_id', 255);
			$table->string('postcode', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('accntng_restitute', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('restitute_id');
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
		
        Schema::create('accntng_recurring', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
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
		
        Schema::create('accntng_demand', function (Blueprint $table) {
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
	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accntng_safe');
        Schema::dropIfExists('accntng_safe_bank');
        Schema::dropIfExists('accntng_safe_activity');
		
        Schema::dropIfExists('accntng_check');
		
        Schema::dropIfExists('accntng_personnel');
        Schema::dropIfExists('accntng_personnel_contact');
        Schema::dropIfExists('accntng_personnel_document');
        Schema::dropIfExists('accntng_personnel_bank_account');
		
        Schema::dropIfExists('accntng_cart');
		
        Schema::dropIfExists('accntng_order');
        Schema::dropIfExists('accntng_order_item');
        Schema::dropIfExists('accntng_order_contact');
        Schema::dropIfExists('accntng_order_financial');
        Schema::dropIfExists('accntng_order_shipment');
        Schema::dropIfExists('accntng_order_shipment_item');
		
        Schema::dropIfExists('accntng_invoice');
        Schema::dropIfExists('accntng_invoice_item');
        Schema::dropIfExists('accntng_invoice_financial');
        Schema::dropIfExists('accntng_invoice_contact');
		
        Schema::dropIfExists('accntng_restitute');
		
        Schema::dropIfExists('accntng_recurring');
		
        Schema::dropIfExists('accntng_demand');
    }

};
