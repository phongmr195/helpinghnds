<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashOutLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_out_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('worker_id');
            $table->integer('card_id');
            $table->string('cash_out_amount');
            $table->tinyInteger('status')->default(0);
            $table->string('current_balance');
            $table->string('transaction_id', 30);
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
        Schema::dropIfExists('cash_out_logs');
    }
}
