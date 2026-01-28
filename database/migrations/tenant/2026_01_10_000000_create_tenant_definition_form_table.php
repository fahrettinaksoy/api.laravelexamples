<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';
	
    public function up()
    {
        Schema::create('def_form_form_element', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('form_element_id');
			$table->string('type', 255);
			$table->tinyInteger('multiple');
			$table->tinyInteger('status');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
		Schema::create('def_form_form_element_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('form_element_translation_id');
			$table->integer('form_element_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('def_form_form_element');
        Schema::dropIfExists('def_form_form_element_translation');
    }

};
