<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('def_acc_cash_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Farklı kasa işlem türlerini (örn: nakit, havale) tanımlar. Ödeme yöntemi sınıflandırmalarını açıklamalar ve görsel işaretleyiciler ile saklar.');

            $table->bigIncrements('type_id')->comment('Kasa türü için birincil anahtar');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->string('name', 255)->unique()->comment('Kasa türü adı (örn: Nakit, Banka Havalesi)');
            $table->string('icon', 255)->nullable()->comment('Kasa türü için simge URL\'si veya yolu');
            $table->string('color', 20)->nullable()->comment('Kasa türü için renk kodu (örn: #FFFFFF)');
            $table->boolean('is_system')->default(false)->comment('Bu sistem tanımlı bir kasa türü mü? (false: Hayır, true: Evet)');
            $table->boolean('status')->default(false)->comment('Kasa türü durumu (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID\'si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');

            $table->index('status', 'idx_cash_type_status');
            $table->index('created_by', 'idx_cash_type_created_by');
            $table->index('updated_by', 'idx_cash_type_updated_by');
            $table->index('is_system', 'idx_cash_type_is_system');
        });
		
        Schema::connection('def_acc_cash_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
	
        Schema::connection('def_acc_transaction_flow', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Ödemeler, iadeler gibi nakit akış süreçlerini öncelik ve durumlarla tanımlar. Finansal işlemleri sistemde kategorize etmek için kullanılır.');

            $table->bigIncrements('flow_id')->comment('Nakit süreci için birincil anahtar');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->string('name', 100)->unique()->comment('Nakit süreci adı (örn: Ödeme, İade)');
            $table->string('icon', 255)->nullable()->comment('Nakit süreci için simge URL\'si veya yolu');
            $table->string('color', 20)->nullable()->comment('Nakit süreci için renk kodu (örn: #FFFFFF)');
            $table->unsignedSmallInteger('priority')->default(0)->comment('Nakit süreci için öncelik sırası');
            $table->boolean('status')->default(false)->comment('Nakit süreci durumu (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID\'si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');

            $table->index('status', 'idx_cash_transaction_flow_status');
            $table->index('created_by', 'idx_cash_transaction_flow_created_by');
            $table->index('updated_by', 'idx_cash_transaction_flow_updated_by');
            $table->index('priority', 'idx_cash_transaction_flow_priority');
        });
		
        Schema::connection('def_acc_transaction_flow_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('flow_translation_id');
			$table->integer('flow_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::connection('def_acc_transaction_group', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Nakit ile ilgili modüller için gruplama kategorilerini tanımlar (örn: muhasebe, işlemler). Finansal modülleri görsel tanımlayıcılarla düzenler.');

            $table->bigIncrements('group_id')->comment('Nakit modül grubu için birincil anahtar');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->string('name', 100)->unique()->comment('Nakit modül grubu adı (örn: Muhasebe, İşlemler)');
            $table->string('icon', 255)->nullable()->comment('Nakit modül grubu için simge URL\'si veya yolu');
            $table->string('color', 20)->nullable()->comment('Nakit modül grubu için renk kodu (örn: #FFFFFF)');
            $table->boolean('status')->default(false)->comment('Nakit modül grubu durumu (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID\'si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');

            $table->index('status', 'idx_cash_transaction_group_status');
            $table->index('created_by', 'idx_cash_transaction_group_created_by');
            $table->index('updated_by', 'idx_cash_transaction_group_updated_by');
        });
		
        Schema::connection('def_acc_transaction_group_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('group_translation_id');
			$table->integer('group_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::connection('def_acc_transaction_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Nakit işlemleri için modül türlerini tanımlar, süreçleri modül gruplarına bağlar. Finansal modüller için yönlendirme bilgileri ve görünürlük ayarları içerir.');

            $table->bigIncrements('type_id')->comment('Nakit modül türü için birincil anahtar');
            $table->string('code', 50)->unique()->nullable()->comment('Benzersiz kod');
            $table->string('name', 100)->unique()->comment('Nakit modül türü adı (örn: Fatura, Ödeme)');
            $table->unsignedBigInteger('flow_id')->comment('Nakit sürecine referans');
            $table->unsignedBigInteger('group_id')->comment('Nakit modül grubuna referans');
            $table->boolean('menu_view')->default(false)->comment('Menüde görünür mü? (false: Hayır, true: Evet)');
            $table->string('request_id', 100)->unique()->comment('Modül için benzersiz istek tanımlayıcısı');
            $table->string('icon', 255)->nullable()->comment('Nakit modül türü için simge URL\'si veya yolu');
            $table->string('color', 20)->nullable()->comment('Nakit modül türü için renk kodu (örn: #FFFFFF)');
            $table->string('module', 100)->comment('Modül adı (örn: muhasebe, faturalama)');
            $table->string('route', 255)->comment('Modül için route yolu');
            $table->boolean('is_visible')->default(false)->comment('Kullanıcılara görünür mü? (false: Hayır, true: Evet)');
            $table->boolean('status')->default(false)->comment('Nakit modül türü durumu (false: Pasif, true: Aktif)');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı ID\'si');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Son güncelleyen kullanıcı ID\'si');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');

            $table->index('status', 'idx_cash_transaction_type_status');
            $table->index('flow_id', 'idx_cash_transaction_type_flow_id');
            $table->index('group_id', 'idx_cash_transaction_type_group_id');
            $table->index('created_by', 'idx_cash_transaction_type_created_by');
            $table->index('updated_by', 'idx_cash_transaction_type_updated_by');
            $table->index('is_visible', 'idx_cash_transaction_type_is_visible');
            $table->index('menu_view', 'idx_cash_transaction_type_menu_view');
        });
		
        Schema::connection('def_acc_transaction_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('def_acc_check_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_id');
			$table->string('color',255);
			$table->string('icon',255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_check_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->text('description');
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('def_acc_order_group', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('group_id');
			$table->string('code', 255);
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_order_group_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('group_translation_id');
			$table->integer('group_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('def_acc_order_operation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('operation_id');
			$table->string('color', 255);
			$table->integer('sort_order')->default(0);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_order_operation_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('operation_translation_id');
			$table->integer('operation_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_order_status', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('status_id');
			$table->integer('operation_id')->default(0);
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('def_acc_invoice_type', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_id');
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_invoice_type_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('type_translation_id');
			$table->integer('type_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('def_acc_invoice_category', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_id');
			$table->integer('parent_id')->default(0);
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_invoice_category_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('category_translation_id');
			$table->integer('category_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });

        Schema::create('def_acc_refund_status', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('status_id');
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_refund_status_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('status_translation_id');
			$table->integer('status_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_refund_action', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('action_id');
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_refund_action_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('action_translation_id');
			$table->integer('action_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_refund_reason', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('reason_id');
			$table->string('color', 255);
			$table->tinyInteger('status')->default(0);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
		
        Schema::create('def_acc_refund_reason_translation', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
            $table->bigIncrements('reason_translation_id');
			$table->integer('reason_id')->default(0);
			$table->string('language_code', 255);
			$table->string('name', 255);
			$table->string('description', 255);
			$table->timestamp('date_modified', 0);
			$table->timestamp('date_created', 0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('def_acc_cash_type');
        Schema::dropIfExists('def_acc_cash_type_translation');

        Schema::dropIfExists('def_acc_transaction_flow');
        Schema::dropIfExists('def_acc_transaction_flow_translation');
        Schema::dropIfExists('def_acc_transaction_group');
        Schema::dropIfExists('def_acc_transaction_group_translation');
        Schema::dropIfExists('def_acc_transaction_type');
        Schema::dropIfExists('def_acc_transaction_type_translation');
		
        Schema::dropIfExists('def_acc_loan_type');
        Schema::dropIfExists('def_acc_loan_type_translation');
		
        Schema::dropIfExists('def_acc_check_type');
        Schema::dropIfExists('def_acc_check_type_translation');
		
        Schema::dropIfExists('def_acc_order_group');
        Schema::dropIfExists('def_acc_order_group_translation');
        Schema::dropIfExists('def_acc_order_operation');
        Schema::dropIfExists('def_acc_order_operation_translation');
        Schema::dropIfExists('def_acc_order_status');
        Schema::dropIfExists('def_acc_order_status_translation');
		
        Schema::dropIfExists('def_acc_invoice_type');
        Schema::dropIfExists('def_acc_invoice_type_translation');
        Schema::dropIfExists('def_acc_invoice_category');
        Schema::dropIfExists('def_acc_invoice_category_translation');
		
        Schema::dropIfExists('def_acc_refund_status');
        Schema::dropIfExists('def_acc_refund_status_translation');
        Schema::dropIfExists('def_acc_refund_action');
        Schema::dropIfExists('def_acc_refund_action_translation');
        Schema::dropIfExists('def_acc_refund_reason');
        Schema::dropIfExists('def_acc_refund_reason_translation');
    }
};
