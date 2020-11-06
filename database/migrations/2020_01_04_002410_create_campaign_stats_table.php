<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('campaign_id')->unsigned();
            $table->integer('pending')->default(0);
            $table->integer('completed')->default(0);
            $table->integer('failed')->default(0);
            $table->foreign('campaign_id')->references('id')->on('campaign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_stats');
    }
}
