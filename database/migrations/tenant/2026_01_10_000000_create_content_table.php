<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cont_page', function (Blueprint $table) {
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
		
        Schema::create('cont_page_translation', function (Blueprint $table) {
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
		
        Schema::create('cont_page_image', function (Blueprint $table) {
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
		
        Schema::create('cont_page_video', function (Blueprint $table) {
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

        Schema::create('cont_blog_post', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('post_id');
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('viewed')->default(0);
			$table->integer('sort_order')->default(0);
			$table->integer('layout_id')->default(0);
			$table->tinyInteger('membership')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('cont_blog_post_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('post_translation_id');
			$table->integer('post_id')->default(0);
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
		
        Schema::create('cont_blog_post_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('post_category_id');
			$table->integer('post_id')->default(0);
			$table->integer('category_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('cont_blog_post_image', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('post_image_id');
			$table->integer('post_id')->default(0);
			$table->string('file', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('cont_blog_post_video', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('post_video_id');
			$table->integer('post_id')->default(0);
            $table->enum('source', ['code', 'url', 'file', 'embed']);
            $table->string('content', 255);
            $table->string('name', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('cont_blog_post_related', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('post_related_id');
			$table->integer('post_id')->default(0);
			$table->integer('related_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('cont_blog_post_product', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('post_product_id');
			$table->integer('post_id')->default(0);
			$table->integer('product_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('cont_blog_comment', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('comment_id');
			$table->integer('post_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->string('code', 255);
			$table->string('author', 255);
			$table->text('content');
			$table->integer('rating')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cont_page');
        Schema::dropIfExists('cont_page_translation');
        Schema::dropIfExists('cont_page_image');
        Schema::dropIfExists('cont_page_video');

        Schema::dropIfExists('cont_blog_post');
        Schema::dropIfExists('cont_blog_post_translation');
        Schema::dropIfExists('cont_blog_post_category');
        Schema::dropIfExists('cont_blog_post_image');
        Schema::dropIfExists('cont_blog_post_video');
        Schema::dropIfExists('cont_blog_post_related');
        Schema::dropIfExists('cont_blog_post_product');
		
        Schema::dropIfExists('cont_blog_comment');
    }
};
