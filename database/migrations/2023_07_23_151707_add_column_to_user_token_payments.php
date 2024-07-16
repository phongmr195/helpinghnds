<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToUserTokenPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_token_payments', function (Blueprint $table) {
            $table->string('payment_3rd', 50)->default('vnpt')->after('last_used_date');
            $table->string('customer_id', 100)->nullable()->after('payment_3rd');
            $table->string('payment_method_id', 100)->nullable()->after('customer_id');
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
            $table->dropColumn(['payment_3rd', 'customer_id', 'payment_method_id']);
        });
    }
}
