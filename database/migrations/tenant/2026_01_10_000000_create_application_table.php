<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('app_application', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_id');
			$table->integer('category_id')->default(0);
			$table->integer('developer_id')->default(0);
			$table->string('type', 255);
			$table->string('code', 255);
			$table->string('icon', 255);
			$table->string('cover', 255);
			$table->string('banner', 255);
			$table->string('video', 255);
			$table->integer('license')->default(0);
			$table->decimal('price', 19, 2);
			$table->string('currency_code', 255);
			$table->integer('tax_class_id')->default(0);
			$table->string('version', 255);
			$table->integer('rating')->default(0);
			$table->integer('download')->default(0);
			$table->string('support_mail', 255);
			$table->string('support_website', 255);
			$table->string('support_document', 255);
			$table->integer('status')->default(0);
			$table->timestamp('date_lastupdate', 0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('app_application_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_translation_id');
			$table->integer('application_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('summary', 255);
			$table->string('tag', 255);
			$table->string('keyword', 255);
			$table->text('description');
			$table->text('requirement');
			$table->string('meta_title', 255);
			$table->string('meta_description', 255);
			$table->string('meta_keyword', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('app_application_image', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_image_id');
			$table->index('application_id');
			$table->integer('application_id')->default(0);
            $table->string('file', 255);
            $table->string('description', 255);
			$table->integer('sort_order')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('app_application_faq', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_faq_id');
			$table->index('application_id');
			$table->integer('application_id')->default(0);
			$table->integer('faq_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('app_application_post', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_post_id');
			$table->index('application_id');
			$table->integer('application_id')->default(0);
			$table->integer('post_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('app_application_related', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_related_id');
			$table->index('application_id');
			$table->integer('application_id')->default(0);
			$table->integer('related_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('app_application_campaign', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_related_id');
			$table->index('application_id');
			$table->integer('application_id')->default(0);
			$table->integer('campaign_id')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('app_application_channel_services', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('application_channel_service_id');
			$table->string('application_code', 255);
			$table->string('channel_service_code', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_application');
        Schema::dropIfExists('app_application_translation');
        Schema::dropIfExists('app_application_image');
        Schema::dropIfExists('app_application_faq');
        Schema::dropIfExists('app_application_related');
        Schema::dropIfExists('app_application_campaign');
        Schema::dropIfExists('app_application_channel_services');
    }
};
