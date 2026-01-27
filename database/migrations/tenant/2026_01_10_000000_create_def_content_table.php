<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('def_cont_blog_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_id');
			$table->integer('parent_id')->default(0);
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('sort_order')->default(0);
			$table->integer('membership')->default(0);
			$table->integer('layout_id')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_cont_blog_category_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_translation_id');
			$table->integer('category_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->text('description');
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('def_cont_blog_category');
        Schema::dropIfExists('def_cont_blog_category_translation');
    }
};
