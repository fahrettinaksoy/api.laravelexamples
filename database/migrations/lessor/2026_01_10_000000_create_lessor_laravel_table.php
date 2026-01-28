<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::connection('conn_lsr')->create('jobs', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('queue')->index();
			$table->longText('payload');
			$table->unsignedTinyInteger('attempts');
			$table->unsignedInteger('reserved_at')->nullable();
			$table->unsignedInteger('available_at');
			$table->unsignedInteger('created_at');
		});

		Schema::connection('conn_lsr')->create('failed_jobs', function (Blueprint $table) {
			$table->id();
			$table->string('uuid')->unique();
			$table->text('connection');
			$table->text('queue');
			$table->longText('payload');
			$table->longText('exception');
			$table->timestamp('failed_at')->useCurrent();
		});

		Schema::connection('conn_lsr')->create('sessions', function (Blueprint $table) {
			$table->string('id')->primary();
			$table->foreignId('user_id')->nullable()->index();
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent')->nullable();
			$table->text('payload');
			$table->integer('last_activity')->index();
		});
	}

	public function down(): void
	{
		//Schema::connection('conn_lsr')->dropIfExists('cache');
		//Schema::connection('conn_lsr')->dropIfExists('cache_locks');
		Schema::connection('conn_lsr')->dropIfExists('jobs');
		Schema::connection('conn_lsr')->dropIfExists('failed_jobs');
		Schema::connection('conn_lsr')->dropIfExists('sessions');
	}
};
