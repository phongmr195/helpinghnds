<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCardBrandToUserTokenPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_token_payments', function (Blueprint $table) {
            $table->string('card_brand', 100)->nullable()->after('bank_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_token_payments', function (Blueprint $table) {
            $table->dropColumn('card_brand');
        });
    }
}
