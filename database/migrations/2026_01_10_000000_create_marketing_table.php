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
	
        Schema::create('mrktng_campaign', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_id');
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('campaign_type_id')->default(0);
			$table->string('campaign_type_code', 255);
			$table->text('campaign_type_setting');
			$table->integer('layout_id')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

		Schema::create('mrktng_campaign_history', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_history_id');
			$table->integer('campaign_id')->default(0);
			$table->integer('order_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->decimal('amount', 19, 2);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_campaign_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_translation_id');
			$table->integer('campaign_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->longText('description');
			$table->longText('condition');
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_campaign_product', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_product_id');
			$table->integer('campaign_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_coupon', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('coupon_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('discount_type', 255);
			$table->decimal('discount_rate', 19, 2);
			$table->integer('limit_account')->default(0);
			$table->integer('limit_usage')->default(0);
			$table->decimal('cart_total', 19, 2);
			$table->timestamp('date_start', 0);
			$table->timestamp('date_end', 0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_coupon_history', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('coupon_history_id');
			$table->integer('coupon_id')->default(0);
			$table->integer('order_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->decimal('amount', 19, 2);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_coupon_product', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('coupon_product_id');
			$table->integer('coupon_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_coupon_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('coupon_category_id');
			$table->integer('coupon_id')->default(0);
			$table->integer('category_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_coupon_brand', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('coupon_brand_id');
			$table->integer('coupon_id')->default(0);
			$table->integer('brand_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_coupon_account_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('coupon_account_group_id');
			$table->integer('coupon_id')->default(0);
			$table->integer('account_group_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('mrktng_gift_voucher', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('gift_voucher_id');
			$table->integer('gift_voucher_theme_id')->default(0);
			$table->integer('order_id')->default(0);
			$table->string('code', 255);
			$table->string('from_name', 255);
			$table->string('from_email', 255);
			$table->string('to_name', 255);
			$table->string('to_email', 255);
			$table->text('message');
			$table->decimal('amount', 15, 2);
			$table->tinyInteger('usage')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_sending', 0);
			$table->timestamp('date_usage', 0);
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
        Schema::dropIfExists('mrktng_campaign');
        Schema::dropIfExists('mrktng_campaign_translation');
        Schema::dropIfExists('mrktng_campaign_product');
		
        Schema::dropIfExists('mrktng_coupon');
        Schema::dropIfExists('mrktng_coupon_history');
        Schema::dropIfExists('mrktng_coupon_product');
        Schema::dropIfExists('mrktng_coupon_category');
        Schema::dropIfExists('mrktng_coupon_brand');
        Schema::dropIfExists('mrktng_coupon_account_group');;
		
        Schema::dropIfExists('mrktng_gift_voucher');
    }

};
