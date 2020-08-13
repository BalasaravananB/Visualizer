<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sitename');
            $table->string('siteurl')->nullable();
            $table->string('accesstoken', 60)->unique();
            $table->string('referer')->nullable();
            $table->string('showprice')->nullable();
            $table->string('buynowtext')->nullable();
            $table->string('product_siteid')->nullable();
            $table->string('product_pageid')->nullable();
            $table->string('dsn')->nullable();
            $table->string('custom_header')->nullable();
            $table->string('custom_footer')->nullable();
            $table->string('use_custom_category')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('application_url')->nullable();
            $table->string('vehicle_dir')->nullable();
            $table->string('wheel_dir')->nullable();
            $table->string('gateway')->nullable(); 
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
        Schema::dropIfExists('client_sites');
    }
}
