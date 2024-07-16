<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone', 12)->unique()->nullable();
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->integer('number_id')->nullable();
            $table->integer('type_number_id')->nullable();
            $table->longText('id_card_before')->nullable();
            $table->longText('id_card_after')->nullable();
            $table->string('user_type')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longtitude', 11, 8)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
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
        Schema::dropIfExists('users');
    }
}
