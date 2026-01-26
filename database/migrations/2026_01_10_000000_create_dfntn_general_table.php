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
	
        Schema::create('dfntn_gnrl_branch', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('branch_id');
			$table->string('code', 255);
			$table->string('type', 255);
			$table->string('name', 255);
			$table->string('company', 255);
			$table->string('authorized', 255);	
			$table->string('image', 255);
			$table->string('website', 255);
			$table->string('email', 255);
			$table->string('phone_number', 255);
			$table->string('fax_number', 255);
			$table->string('gsm_number', 255);
			$table->string('country_id', 255);
			$table->string('city_id', 255);
			$table->string('district_id', 255);
			$table->string('postcode', 255);
			$table->string('address_1', 255);
			$table->string('address_2', 255);
			$table->string('map_coordinate', 255);
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
        Schema::dropIfExists('dfntn_gnrl_branch');
    }

};
