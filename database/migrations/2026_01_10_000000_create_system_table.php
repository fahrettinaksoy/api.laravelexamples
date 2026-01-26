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
	
        Schema::create('systm_application', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_value_id');
			$table->string('type', 255);
			$table->string('application', 255);
			$table->text('value');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('systm_setting', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('setting_id');
			$table->string('option', 255);
			$table->string('slug', 255);
			$table->text('value');
			$table->tinyInteger('serialized')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('systm_company', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('definition_id');
			$table->string('option', 255);
			$table->string('slug', 255);
			$table->text('value');
			$table->tinyInteger('view')->default(0);
			$table->tinyInteger('serialized')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('systm_method', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('method_id');
			$table->string('method_group', 255);
			$table->string('method_type', 255);
			$table->text('setting');
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('systm_plugin', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('plugin_id');
			$table->string('plugin_group', 255);
			$table->string('plugin_type', 255);
			$table->text('setting');
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('systm_widget', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('widget_id');
			$table->string('widget_type', 255);
			$table->string('name', 255);
			$table->string('css', 255);
			$table->string('html', 255);
			$table->text('setting');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
	
        Schema::create('systm_variable', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('variable_id');
			$table->string('variable_key', 255)->unique();
			$table->text('variable_value')->nullable();
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
        Schema::dropIfExists('systm_component');
		
        Schema::dropIfExists('systm_setting');
        Schema::dropIfExists('systm_company');
		
        Schema::dropIfExists('systm_method');
        Schema::dropIfExists('systm_plugin');
        Schema::dropIfExists('systm_widget');
        Schema::dropIfExists('systm_variable');
    }

};
