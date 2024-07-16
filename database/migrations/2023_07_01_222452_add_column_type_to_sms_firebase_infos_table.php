<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTypeToSmsFirebaseInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_firebase_infos', function (Blueprint $table) {
            $table->string('type', 50)->after('status')->nullable();
            $table->char('code', 10)->after('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_firebase_infos', function (Blueprint $table) {
            $table->dropColumn(['type', 'code']);
        });
    }
}
