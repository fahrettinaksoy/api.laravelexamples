<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    public function up()
    {

        Schema::create('mrktplc_pairing_brand', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->bigIncrements('pairing_brand_id');
            $table->integer('channel_id')->default(0);
            $table->string('channel_code', 255);
            $table->integer('matching_id')->default(0);
            $table->string('matching_code', 255);
            $table->integer('matched_id')->default(0);
            $table->string('matched_code', 255);
            $table->string('matched_name', 255);
            $table->longText('matched_attributes');
            $table->longText('matched_attribute_select');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('date_modified', 0);
            $table->timestamp('date_created', 0);
        });

        Schema::create('mrktplc_pairing_category', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->bigIncrements('pairing_category_id');
            $table->integer('channel_id')->default(0);
            $table->string('channel_code', 255);
            $table->integer('matching_id')->default(0);
            $table->string('matching_code', 255);
            $table->integer('matched_id')->default(0);
            $table->string('matched_code', 255);
            $table->string('matched_name', 255);
            $table->longText('matched_attributes');
            $table->longText('matched_attribute_select');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('date_modified', 0);
            $table->timestamp('date_created', 0);
        });

        Schema::create('mrktplc_pairing_product', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->bigIncrements('pairing_product_id');
            $table->integer('channel_id')->default(0);
            $table->string('channel_code', 255);
            $table->integer('matching_id')->default(0);
            $table->string('matching_code', 255);
            $table->integer('matched_id')->default(0);
            $table->string('matched_code', 255);
            $table->string('matched_name', 255);
            $table->string('matched_batch_id', 255);
            $table->decimal('matched_price', 19, 2);
            $table->string('matched_currency_code', 255);
            $table->integer('matched_tax_class_id')->default(0);
            $table->integer('matched_quantity')->default(0);
            $table->longText('matched_attributes');
            $table->longText('matched_attribute_select');
            $table->longText('matched_response');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('date_modified', 0);
            $table->timestamp('date_created', 0);
        });

        Schema::create('mrktplc_pairing_warehouse', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->bigIncrements('pairing_warehouse_id');
            $table->integer('channel_id')->default(0);
            $table->string('channel_code', 255);
            $table->integer('matching_id')->default(0);
            $table->string('matching_code', 255);
            $table->integer('matched_id')->default(0);
            $table->string('matched_code', 255);
            $table->string('matched_name', 255);
            $table->longText('matched_attributes');
            $table->longText('matched_attribute_select');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('date_modified', 0);
            $table->timestamp('date_created', 0);
        });

        Schema::create('mrktplc_pairing_cargo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->bigIncrements('pairing_cargo_id');
            $table->integer('channel_id')->default(0);
            $table->string('channel_code', 255);
            $table->integer('matching_id')->default(0);
            $table->string('matching_code', 255);
            $table->integer('matched_id')->default(0);
            $table->string('matched_code', 255);
            $table->string('matched_name', 255);
            $table->longText('matched_attributes');
            $table->longText('matched_attribute_select');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('date_modified', 0);
            $table->timestamp('date_created', 0);
        });

        Schema::create('mrktplc_pairing_filter', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->bigIncrements('pairing_filter_id');
            $table->integer('channel_id')->default(0);
            $table->string('channel_code', 255);
            $table->integer('matching_id')->default(0);
            $table->string('matching_code', 255);
            $table->integer('matched_id')->default(0);
            $table->string('matched_code', 255);
            $table->string('matched_name', 255);
            $table->longText('matched_attributes');
            $table->longText('matched_attribute_select');
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('mrktplc_pairing_brand');
        Schema::dropIfExists('mrktplc_pairing_category');
        Schema::dropIfExists('mrktplc_pairing_product');
        Schema::dropIfExists('mrktplc_pairing_warehouse');
        Schema::dropIfExists('mrktplc_pairing_cargo');
        Schema::dropIfExists('mrktplc_pairing_filter');
    }
};
