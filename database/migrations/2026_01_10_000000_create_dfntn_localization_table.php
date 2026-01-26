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
	
        Schema::create('dfntn_lclztn_language', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('language_id');
			$table->string('name', 255);
			$table->string('code', 255);
			$table->string('locale', 255);
			$table->string('image', 255);
			$table->string('directory', 255);
			$table->string('direction', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_currency', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('currency_id');
			$table->string('name', 255);
			$table->string('code', 255);
			$table->string('icon', 255);
			$table->string('symbol_left', 255);
			$table->string('symbol_right', 255);
			$table->string('decimal_place', 255);
			$table->string('decimal_point', 255);
			$table->string('thousand_point', 255);
			$table->double('value', 15, 8);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_country', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('country_id');
			$table->string('name', 255);
			$table->string('title', 255);
			$table->string('iso_code_2', 255);
			$table->string('iso_code_3', 255);
			$table->string('address_format', 255);
			$table->tinyInteger('postcode_required')->default(0);
			$table->string('currency_code', 255);
			$table->string('language_code', 255);
			$table->string('time_zone', 255);
			$table->string('phone_code', 255);
			$table->string('domain_extension', 255);
			$table->string('date_format', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_city', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('city_id');
			$table->integer('country_id')->default(0);
			$table->string('name', 255);
			$table->string('iso_code_2', 255);
			$table->string('iso_code_3', 255);
			$table->string('traffic_code', 255);
			$table->string('phone_code', 255);
			$table->text('svg_path');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_district', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('district_id');
			$table->integer('country_id')->default(0);
			$table->integer('city_id')->default(0);
			$table->string('name', 255);
			$table->string('post_code', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_geo', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('geo_id');
			$table->string('name', 255);
			$table->string('description', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_geo_zone', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('geo_zone_id');
			$table->integer('geo_id')->default(0);
			$table->integer('country_id')->default(0);
			$table->integer('city_id')->default(0);
			$table->integer('district_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_length_class', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('length_class_id');
			$table->decimal('value', 15, 8);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_length_class_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('length_class_translation_id');
			$table->integer('length_class_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('abbreviation', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_weight_class', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('weight_class_id');
			$table->decimal('value', 15, 8);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_weight_class_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('weight_class_translation_id');
			$table->integer('weight_class_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('abbreviation', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_unit', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('unit_id');
			$table->decimal('value', 15, 8);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_unit_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('unit_translation_id');
			$table->integer('unit_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('abbreviation', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_tax_class', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('tax_class_id');
			$table->integer('geo_id')->default(0);
			$table->string('type', 255);
			$table->decimal('rate', 19, 2);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_tax_class_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('tax_class_translation_id');
			$table->integer('tax_class_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('short', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_tax_class_account_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('tax_class_account_group_id');
			$table->integer('tax_class_id')->default(0);
			$table->integer('account_group_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_tax_rate', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('tax_rate_id');
			$table->integer('geo_id')->default(0);
			$table->decimal('rate', 19, 2);
			$table->string('name', 255);
			$table->string('type',255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_tax_rate_account_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('tax_rate_account_group_id');
			$table->integer('tax_rate_id')->default(0);
			$table->integer('account_group_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_address_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('address_type_id');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_lclztn_address_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('address_type_translation_id');
			$table->integer('address_type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
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
        Schema::dropIfExists('dfntn_lclztn_language');
        Schema::dropIfExists('dfntn_lclztn_currency');
        Schema::dropIfExists('dfntn_lclztn_country');
        Schema::dropIfExists('dfntn_lclztn_city');
        Schema::dropIfExists('dfntn_lclztn_district');
        Schema::dropIfExists('dfntn_lclztn_geo');
        Schema::dropIfExists('dfntn_lclztn_geo_zone');
        Schema::dropIfExists('dfntn_lclztn_length_class');
        Schema::dropIfExists('dfntn_lclztn_length_class_translation');
        Schema::dropIfExists('dfntn_lclztn_weight_class');
        Schema::dropIfExists('dfntn_lclztn_weight_class_translation');
        Schema::dropIfExists('dfntn_lclztn_unit');
        Schema::dropIfExists('dfntn_lclztn_unit_translation');
        Schema::dropIfExists('dfntn_lclztn_tax_class');
        Schema::dropIfExists('dfntn_lclztn_tax_class_translation');
        Schema::dropIfExists('dfntn_lclztn_tax_class_account_group');
        Schema::dropIfExists('dfntn_lclztn_address_type');
        Schema::dropIfExists('dfntn_lclztn_address_type_translation');
    }

};
