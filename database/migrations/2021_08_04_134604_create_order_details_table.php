<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_id');
            $table->tinyInteger('status_id')->default(0);
            $table->dateTime('begin_at')->nullable();
            $table->dateTime('begin_end')->nullable();
            $table->dateTime('begin_pause')->nullable();
            $table->dateTime('cancel_at')->nullable();
            $table->string('price')->nullable();
            $table->longText('note_description')->nullable();
            $table->string('phone', 12)->nullable();
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
        Schema::dropIfExists('order_details');
    }
}
