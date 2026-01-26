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
	
        Schema::create('dfntn_rpstry_block', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('block_id');
			$table->integer('warehouse_id')->default(0);
			$table->integer('category_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('authorized', 255);
			$table->string('width');
			$table->string('length');
			$table->string('height');
			$table->string('description', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_rpstry_area', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('area_id');
			$table->integer('warehouse_id')->default(0);
			$table->integer('block_id')->default(0);
			$table->integer('category_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('authorized', 255);
			$table->string('width');
			$table->string('length');
			$table->string('height');
			$table->string('description', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('dfntn_rpstry_shelf', function (Blueprint $table) {
            $table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('shelf_id');
			$table->integer('warehouse_id')->default(0);
			$table->integer('area_id')->default(0);
			$table->integer('category_id')->default(0);
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('authorized', 255);
			$table->string('width');
			$table->string('length');
			$table->string('height');
			$table->string('description', 255);
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
        Schema::dropIfExists('dfntn_rpstry_block');
        Schema::dropIfExists('dfntn_rpstry_area');
        Schema::dropIfExists('dfntn_rpstry_shelf');
    }

};
