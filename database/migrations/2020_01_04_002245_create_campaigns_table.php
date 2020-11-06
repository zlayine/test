<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->integer('smtp_group')->nullable();
            $table->integer('rotation')->nullable();
            $table->string('charset')->nullable();
            $table->string('encoding')->nullable();
            $table->string('encryption')->nullable();
            $table->string('helo')->nullable();
            $table->string('content_type')->nullable();
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('subject')->nullable();
            $table->longText('header')->nullable();
            $table->longText('body')->nullable();
            $table->longText('alt')->nullable();
            $table->integer('iscal')->nullable();
            $table->longText('cal')->nullable();
            $table->integer('status')->nullable();
            $table->integer('auto_tls')->nullable();
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
        Schema::dropIfExists('campaigns');
    }
}
