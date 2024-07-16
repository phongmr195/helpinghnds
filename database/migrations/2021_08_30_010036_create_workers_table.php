<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique()->index()->nullable();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone', 15)->index()->unique();
            $table->string('address')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('is_working')->default(0);
            $table->tinyInteger('is_online')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('lockout_time')->default(0);
            $table->integer('number_id')->nullable();
            $table->integer('type_number_id')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longtitude', 11, 8)->nullable();
            $table->string('password');
            $table->text('device_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workers');
    }
}
