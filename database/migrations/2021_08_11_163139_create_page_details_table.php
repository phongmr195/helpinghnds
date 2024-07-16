<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('page_id');
            $table->integer('block_id');
            $table->string('type');
            $table->string('controller')->nullable();
            $table->string('action')->nullable();
            $table->string('method')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('page_details');
    }
}
