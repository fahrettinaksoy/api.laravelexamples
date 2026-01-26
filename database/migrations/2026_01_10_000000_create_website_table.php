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
	
        Schema::create('wbst_page', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('page_id');
			$table->tinyInteger('membership')->default(0);
			$table->tinyInteger('legal')->default(0);
			$table->string('code', 255);
			$table->string('html', 255);
			$table->string('image', 255);
			$table->integer('viewed')->default(0);
			$table->integer('sort_order')->default(0);
			$table->integer('layout_id')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_page_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('page_translation_id');
			$table->integer('page_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->string('tag', 255);
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_page_image', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('page_image_id');
			$table->integer('page_id')->default(0);
            $table->string('file', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_page_video', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('page_video_id');
			$table->integer('page_id')->default(0);
            $table->enum('source', ['code', 'url', 'file', 'embed']);
            $table->string('content', 255);
            $table->string('name', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
	
        Schema::create('wbst_layout', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('layout_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->text('summary');
			$table->text('position');
			$table->string('path', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_layout_widget', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('layout_widget_id');
			$table->integer('layout_id')->default(0);
			$table->string('position', 255);
			$table->text('widgets');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_banner', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('banner_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_banner_item', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('banner_item_id');
			$table->tinyInteger('banner_id')->default(0);
			$table->string('language_code', 255);
			$table->string('image', 255);
			$table->string('title', 255);
			$table->string('title2', 255);
			$table->string('title3', 255);
			$table->text('summary');
			$table->string('button', 255);
			$table->string('button2', 255);
			$table->string('button3', 255);
			$table->string('link', 255);
			$table->string('link2', 255);
			$table->string('link3', 255);
			$table->text('display');
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_menu', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('menu_id');
			$table->string('code', 255);
			$table->string('type', 255);
			$table->string('name', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_menu_item', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('menu_item_id');
			$table->integer('menu_id')->default(0);
			$table->integer('parent_id')->default(0);
			$table->integer('url_id')->default(0);			// Kalkacak
			
			$table->string('type', 255);
			$table->string('module_action', 255);
			$table->integer('module_id')->default(0);
			$table->string('query', 255);
			$table->string('target', 255);
			$table->string('route', 255);
			
			$table->string('icon', 255);
			$table->string('id_name', 255);
			$table->string('class_name', 255);
			$table->string('style', 255);
			$table->string('image', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_menu_item_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('menu_item_translation_id');
			$table->integer('menu_item_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_url', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('url_id');
			$table->string('code', 255);
			$table->integer('locked')->default(0);
			$table->integer('menu')->default(0);
			$table->integer('linking')->default(0);
			$table->string('module_type', 255);
			$table->integer('module_id')->default(0);
			$table->string('module_query', 255);
			$table->string('module_pattern', 255);
			$table->string('module_controller', 255);
			$table->string('module_action', 255);
			$table->integer('module_membership')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('wbst_url_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('url_translation_id');
			$table->integer('url_id')->default(0);
			$table->string('language_code', 255);
			$table->string('keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
		Schema::create('wbst_form', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('form_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('html', 255);
			$table->text('send');
			$table->text('content');
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
		Schema::create('wbst_form_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('form_translation_id');
			$table->integer('form_id')->default(0);
			$table->string('language_code', 255);
			$table->string('title', 255);
			$table->text('description');
			$table->string('button', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
		Schema::create('wbst_form_incoming', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('form_incoming_id');
			$table->integer('form_id')->default(0);
			$table->text('content');
			$table->string('ip_address', 255);
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
        Schema::dropIfExists('wbst_page');
        Schema::dropIfExists('wbst_page_translation');
        Schema::dropIfExists('wbst_page_image');
        Schema::dropIfExists('wbst_page_video');
		
        Schema::dropIfExists('wbst_layout');
        Schema::dropIfExists('wbst_layout_widget');
		
        Schema::dropIfExists('wbst_banner');
        Schema::dropIfExists('wbst_banner_item');
		
        Schema::dropIfExists('wbst_menu');
        Schema::dropIfExists('wbst_menu_item');
        Schema::dropIfExists('wbst_menu_item_translation');
		
        Schema::dropIfExists('wbst_url');
        Schema::dropIfExists('wbst_url_translation');
		
        Schema::dropIfExists('wbst_form');
        Schema::dropIfExists('wbst_form_translation');
        Schema::dropIfExists('wbst_form_incoming');
    }

};
