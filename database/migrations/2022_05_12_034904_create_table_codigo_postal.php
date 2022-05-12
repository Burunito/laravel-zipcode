<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCodigoPostal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('zip_code', 5);
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('federal_entity_id');
            $table->unsignedBigInteger('municipality_id');
            $table->unsignedBigInteger('city_id');
            $table->foreign('locality_id')->references('id')->on('localities');
            $table->foreign('federal_entity_id')->references('id')->on('federal_entities');
            $table->foreign('municipality_id')->references('id')->on('municipalities');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postal_codes', function (Blueprint $table) {
            $table->dropForeign(['locality_id']);
            $table->dropForeign(['federal_entity_id']);
            $table->dropForeign(['municipality_id']);
            $table->dropForeign(['city_id']);
        });
        
        Schema::dropIfExists('postal_codes');
    }
}
