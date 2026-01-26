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

        Schema::create('wbst_account', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_id');
			$table->integer('type_id')->default(0);
			$table->integer('group_id')->default(0);
			$table->string('code', 255);
			$table->integer('shape')->default(0);
			$table->string('language_code', 255);
			$table->string('image', 255);
			$table->string('name', 255);
			$table->string('tax_office', 255);
			$table->string('tax_number', 255);
			$table->string('trade_chamber', 255);
			$table->string('trade_number', 255);
			$table->string('mersis_number', 255);
			$table->string('kep_address', 255);
			$table->string('email', 255);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verified_token');
			$table->string('password', 255);
			$table->tinyInteger('safe')->default(0);
			$table->tinyInteger('newsletter')->default(0);
			$table->integer('default_authorized_id')->default(0);
			$table->integer('default_contact_id')->default(0);
			$table->integer('default_bank_account_id')->default(0);
			$table->string('ip_address', 255);
            $table->rememberToken();
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_password_reset', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
		
        Schema::create('wbst_account_authorized', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_authorized_id');
			$table->integer('account_id')->default(0);
			$table->string('firstname', 255);
			$table->string('lastname', 255);
			$table->string('citizenship_number', 255);
			$table->integer('gender')->default(0);
			$table->timestamp('birth_date', 0)->nullable();
			$table->string('title', 255);
			$table->string('description', 255);
			$table->string('phone_constant', 255);
			$table->string('phone_mobile', 255);
			$table->string('email', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_contact', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_contact_id');
			$table->integer('account_id')->default(0);
			$table->integer('taxpayer_type_id')->default(0);
			$table->integer('address_type_id')->default(0);
			$table->tinyInteger('abroad')->default(0);
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
			$table->integer('country_id')->default(0);
			$table->integer('city_id')->default(0);
			$table->integer('district_id')->default(0);
			$table->string('postcode', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_bank_account', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_bank_account_id');
			$table->integer('account_id')->default(0);
			$table->integer('bank_id')->default(0);
			$table->string('currency_code', 255);
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
		
        Schema::create('wbst_account_download', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_download_id');
			$table->integer('account_id')->default(0);
			$table->integer('order_id')->default(0);
			$table->integer('download_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_reward', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_reward_id');
			$table->integer('account_id')->default(0);
			$table->integer('order_id')->default(0);
			$table->integer('point')->default(0);
			$table->tinyInteger('entry')->default(0);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_wishlist', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('wishlist_id');
			$table->integer('account_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_wishlist_item', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('wishlist_item_id');
			$table->integer('wishlist_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_falling', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('falling_id');
			$table->integer('account_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_preorder', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('preorder_id');
			$table->string('code', 255);
			$table->integer('account_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->integer('quantity')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_account_token_reset', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_token_reset_id');
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
		
        Schema::create('wbst_account_token_access', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('account_token_access_id');
			$table->morphs('tokenable');
			$table->string('name', 255);
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('wbst_account');
        Schema::dropIfExists('wbst_account_authorized');
        Schema::dropIfExists('wbst_account_contact');
        Schema::dropIfExists('wbst_account_bank_account');
        Schema::dropIfExists('wbst_account_download');
        Schema::dropIfExists('wbst_account_reward');
        Schema::dropIfExists('wbst_account_wishlist');
        Schema::dropIfExists('wbst_account_wishlist_item');
        Schema::dropIfExists('wbst_account_falling');
        Schema::dropIfExists('wbst_account_preorder');
		
        Schema::dropIfExists('wbst_account_authorized_token_reset');
        Schema::dropIfExists('wbst_account_token_access');
    }

};
