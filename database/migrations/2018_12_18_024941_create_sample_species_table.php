<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleSpeciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('species', function (Blueprint $table) {
          $table->increments('id');
          $table->string('specie_id',50);
          $table->string('code',50)->nullable();
          $table->integer('plot_id')->unsigned();
          $table->enum('type', ['herb', 'shrub','arbor'])->nullable();//herb,草本,shrub,灌木,arbor,乔木
          $table->json('data');
          $table->foreign('plot_id')->references('id')->on('plots')
              ->onUpdate('cascade')->onDelete('cascade');
          $table->string('name',50)->nullable();
          $table->decimal('latin_name',50)->nullable();
          $table->timestamp('upload_at')->nullable();
          $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
          $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('sample_species');
    }
}
