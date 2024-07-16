<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddCardLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_card_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('access_token', 50);
            $table->string('order_id');
            $table->integer('user_id');
            $table->tinyInteger('status')->default(false);
            $table->longText('response')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('add_card_logs');
    }
}
