<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ENGINE = 'InnoDB';
    private const CHARSET = 'utf8mb4';
    private const COLLATION = 'utf8mb4_unicode_ci';

    private const TABLE_BLOCK = 'def_wms_block';
    private const TABLE_AREA = 'def_wms_area';
    private const TABLE_SHELF = 'def_wms_shelf';

    public function up(): void
    {
        Schema::create(self::TABLE_BLOCK, function (Blueprint $table) {
            $table->comment('Depo blok tanımları (A Blok, B Blok vb.)');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('block_id')->comment('Blok benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('warehouse_id')->index()->comment('Bağlı olduğu depo kimliği');
            $table->unsignedBigInteger('category_id')->default(0)->comment('Kategori/Tip kimliği');
            $table->string('code', 50)->unique()->comment('Blok kodu');
            $table->string('name', 100)->comment('Blok adı');
            $table->string('authorized_person', 100)->nullable()->comment('Blok sorumlusu');
            $table->decimal('width', 10, 2)->nullable()->comment('Genişlik (cm/m)');
            $table->decimal('length', 10, 2)->nullable()->comment('Uzunluk (cm/m)');
            $table->decimal('height', 10, 2)->nullable()->comment('Yükseklik (cm/m)');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->boolean('status')->default(true)->index()->comment('Durum (Aktif/Pasif)');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_block_soft_delete');
        });

        Schema::create(self::TABLE_AREA, function (Blueprint $table) {
            $table->comment('Depo alan/koridor tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('area_id')->comment('Alan benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('warehouse_id')->index()->comment('Depo kimliği');
            $table->unsignedBigInteger('block_id')->index()->comment('Blok kimliği');
            $table->unsignedBigInteger('category_id')->default(0)->comment('Kategori/Tip kimliği');
            $table->string('code', 50)->unique()->comment('Alan kodu');
            $table->string('name', 100)->comment('Alan adı');
            $table->string('authorized_person', 100)->nullable()->comment('Alan sorumlusu');
            $table->decimal('width', 10, 2)->nullable()->comment('Genişlik');
            $table->decimal('length', 10, 2)->nullable()->comment('Uzunluk');
            $table->decimal('height', 10, 2)->nullable()->comment('Yükseklik');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_area_soft_delete');
        });

        Schema::create(self::TABLE_SHELF, function (Blueprint $table) {
            $table->comment('Depo raf tanımları');
            $table->engine = self::ENGINE;
            $table->charset = self::CHARSET;
            $table->collation = self::COLLATION;

            $table->bigIncrements('shelf_id')->comment('Raf benzersiz kimliği');
            $table->uuid('uuid')->unique()->comment('Evrensel benzersiz tanımlayıcı');
            $table->unsignedBigInteger('warehouse_id')->index()->comment('Depo kimliği');
            $table->unsignedBigInteger('area_id')->index()->comment('Alan kimliği');
            $table->unsignedBigInteger('category_id')->default(0)->comment('Kategori/Tip kimliği');
            $table->string('code', 50)->unique()->comment('Raf kodu');
            $table->string('name', 100)->comment('Raf adı');
            $table->string('authorized_person', 100)->nullable()->comment('Raf sorumlusu');
            $table->decimal('width', 10, 2)->nullable()->comment('Genişlik');
            $table->decimal('length', 10, 2)->nullable()->comment('Uzunluk');
            $table->decimal('height', 10, 2)->nullable()->comment('Yükseklik');
            $table->decimal('max_weight', 10, 2)->nullable()->comment('Maksimum ağırlık kapasitesi');
            $table->text('description')->nullable()->comment('Açıklama');
            $table->boolean('status')->default(true)->index()->comment('Durum');
            
            $table->unsignedBigInteger('created_by')->nullable()->comment('Kaydı oluşturan kullanıcı kimliği');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Kaydı güncelleyen kullanıcı kimliği');
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturma zamanı');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Kayıt son güncelleme zamanı');
            $table->timestamp('deleted_at')->nullable()->comment('Soft delete zamanı');

            $table->index('deleted_at', 'idx_shelf_soft_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_SHELF);
        Schema::dropIfExists(self::TABLE_AREA);
        Schema::dropIfExists(self::TABLE_BLOCK);
    }
};
