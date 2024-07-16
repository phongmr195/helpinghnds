<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_otps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('phone', 15);
            $table->char('otp', 6);
            $table->char('type', 20);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('sms_otps');
    }
}
