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
	
        Schema::create('spprt_faq_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('faq_group_id');
			$table->string('code', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_faq_group_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('faq_group_translation_id');
			$table->integer('faq_group_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_faq', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('faq_id');
			$table->string('code', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('membership')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_faq_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('faq_translation_id');
			$table->integer('faq_id')->default(0);
			$table->string('language_code', 255);
			$table->string('question', 255);
			$table->text('answer');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_faq_faq_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('faq_faq_group_id');
			$table->integer('faq_id')->default(0);
			$table->integer('faq_group_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
	
        Schema::create('spprt_knowledge_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_id');
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('parent_id')->default(0);
			$table->integer('layout_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('membership')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_knowledge_category_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_translation_id');
			$table->integer('category_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->string('keyword', 255);
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_knowledge_article', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('article_id');
			$table->string('code', 255);
			$table->string('image', 255);
			$table->integer('viewed')->default(0);
			$table->integer('layout_id')->default(0);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('membership')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_knowledge_article_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('article_translation_id');
			$table->integer('article_id')->default(0);
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
		
        Schema::create('spprt_knowledge_article_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('article_category_id');
			$table->integer('article_id')->default(0);
			$table->integer('category_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_knowledge_article_image', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('article_image_id');
			$table->integer('article_id')->default(0);
			$table->string('file', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_knowledge_article_video', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('article_video_id');
			$table->integer('article_id')->default(0);
            $table->enum('source', ['code', 'url', 'file', 'embed']);
            $table->string('content', 255);
            $table->string('name', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_knowledge_article_related', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('article_related_id');
			$table->integer('article_id')->default(0);
			$table->integer('related_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_feedback', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('feedback_id');
			$table->string('code', 255);
			$table->string('name', 255);
			$table->string('email', 255);
			$table->string('subject', 255);
			$table->string('message', 255);
			$table->integer('assistant_id')->default(0);
			$table->integer('department_id')->default(0);
			$table->integer('priority_id')->default(0);
			$table->integer('point')->default(0);
			$table->tinyInteger('read')->default(0);
			$table->tinyInteger('status_id')->default(0);
			$table->string('ip_created', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_ticket', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('ticket_id');
			$table->string('code', 255);
			$table->string('subject', 255);
			$table->integer('account_id')->default(0);
			$table->integer('assistant_id')->default(0);
			$table->integer('department_id')->default(0);
			$table->integer('priority_id')->default(0);
			$table->integer('relation_id')->default(0);
			$table->integer('resource_id')->default(0);
			$table->integer('status_id')->default(0);
			$table->string('ip_modified', 255);
			$table->string('ip_created', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('spprt_ticket_meeting', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('ticket_meeting_id');
			$table->integer('ticket_id')->default(0);
			$table->integer('account_id')->default(0);
			$table->integer('assistant_id')->default(0);
			$table->text('message');
			$table->tinyInteger('read')->default(0);
			$table->string('ip_created', 255);
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
        Schema::dropIfExists('spprt_faq_group');
        Schema::dropIfExists('spprt_faq_group_translation');
        Schema::dropIfExists('spprt_faq');
        Schema::dropIfExists('spprt_faq_translation');
        Schema::dropIfExists('spprt_faq_faq_group');
		
        Schema::dropIfExists('spprt_knowledge_category');
        Schema::dropIfExists('spprt_knowledge_category_translation');
        Schema::dropIfExists('spprt_knowledge_article');
        Schema::dropIfExists('spprt_knowledge_article_translation');
        Schema::dropIfExists('spprt_knowledge_article_category');
        Schema::dropIfExists('spprt_knowledge_article_image');
        Schema::dropIfExists('spprt_knowledge_article_video');
        Schema::dropIfExists('spprt_knowledge_article_related');
		
        Schema::dropIfExists('spprt_feedback');
		
        Schema::dropIfExists('spprt_ticket');
        Schema::dropIfExists('spprt_ticket_meeting');
    }

};
