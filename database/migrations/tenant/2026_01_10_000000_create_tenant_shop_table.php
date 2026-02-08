<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_tnt';

    private const ENGINE = 'InnoDB';

    private const CHARSET = 'utf8mb4';

    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_CART = 'shp_cart';

    private const TABLE_CART_ITEM = 'shp_cart_item';

    public function up(): void
    {
        Schema::create(self::TABLE_CART, function (Blueprint $table) {
            $table->comment('Alışveriş sepeti ana tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('cart_id')->comment('Sepet benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->string('session_id', 255)->index()->comment('Oturum kimliği (Session ID)');
            $table->unsignedBigInteger('account_id')->default(0)->index()->comment('Müşteri kimliği (0 ise ziyaretçi)');
            $table->char('currency_code', 3)->default('TRY')->comment('Sepet para birimi');
            $table->char('language_code', 5)->default('tr')->comment('Sepet dili');
            $table->ipAddress('ip_address')->nullable()->comment('Oluşturan IP adresi');
            $table->string('user_agent', 500)->nullable()->comment('Tarayıcı bilgisi');
            $table->boolean('is_active')->default(true)->comment('Aktif mi? (Siparişleşince pasif olur)');
            $table->boolean('is_locked')->default(false)->comment('Kilitli mi? (Ödeme aşamasında)');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index(['account_id', 'is_active'], 'idx_cart_account_active');
            $table->index(['session_id', 'is_active'], 'idx_cart_session_active');
            $table->index('deleted_at', 'idx_cart_soft_delete');
        });

        Schema::create(self::TABLE_CART_ITEM, function (Blueprint $table) {
            $table->comment('Sepet ürün/kalem tablosu');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('cart_item_id')->comment('Sepet kalemi benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('cart_id')->comment('Sepet kimliği');
            $table->unsignedBigInteger('product_id')->comment('Ürün kimliği');
            $table->unsignedInteger('quantity')->default(1)->comment('Adet');
            $table->longText('options')->nullable()->comment('Ürün seçenekleri ve varyasyonlar (JSON)');
            $table->boolean('is_selected')->default(true)->comment('Seçili mi? (Sepette ama alınmayacaklar için)');

            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');

            $table->index('cart_id', 'idx_cart_item_cart');
            $table->index('product_id', 'idx_cart_item_product');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_CART_ITEM);
        Schema::dropIfExists(self::TABLE_CART);
    }
};
