<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplePlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plots', function (Blueprint $table) {
          $table->increments('id');
          $table->string('plot_id',50);
          $table->string('code',50)->nullable();
          $table->integer('land_id')->unsigned();
          $table->enum('type', ['herb', 'shrub','arbor'])->nullable();//herb,草本,shrub,灌木,arbor,乔木
          $table->json('data');
          $table->foreign('land_id')->references('id')->on('lands')->onUpdate('cascade')->onDelete('cascade');
          $table->decimal('lat', 10, 8)->nullable();
          $table->decimal('lng', 10, 8)->nullable();
          $table->decimal('alt',10, 2)->nullable();
          $table->string('investigator_name')->nullable();
          $table->datetime('investigated_at')->nullable();
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
        Schema::dropIfExists('plots');
    }
}
