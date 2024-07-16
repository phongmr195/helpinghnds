<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsFirebaseInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_firebase_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('phone', 15);
            $table->tinyInteger('status')->default(false);
            $table->text('session_info');
            $table->timestamp('expired_date');
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
        Schema::dropIfExists('sms_firebase_infos');
    }
}
