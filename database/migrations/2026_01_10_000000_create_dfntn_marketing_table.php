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
	
        Schema::create('dfntn_mrktng_campaign_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
			$table->bigIncrements('campaign_type_id');
			$table->string('code', 255);
			$table->string('image', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_mrktng_campaign_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('campaign_type_translation_id');
			$table->integer('campaign_type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_mrktng_gift_voucher_theme', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
			$table->bigIncrements('gift_voucher_theme_id');
			$table->string('image', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_mrktng_gift_voucher_theme_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('gift_voucher_theme_translation_id');
			$table->integer('gift_voucher_theme_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
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
        Schema::dropIfExists('dfntn_mrktng_campaign_type');
        Schema::dropIfExists('dfntn_mrktng_campaign_type_translation');
		
        Schema::dropIfExists('dfntn_mrktng_gift_voucher_theme');
        Schema::dropIfExists('dfntn_mrktng_gift_voucher_theme_translation');
    }

};
