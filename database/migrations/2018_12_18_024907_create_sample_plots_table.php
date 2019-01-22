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
          $table->string('code',50);
          $table->string('land_id',50)->unsigned();
          $table->enum('type', ['herb', 'shrub','arbor']);//herb,草本,shrub,灌木,arbor,乔木
          $table->json('data');
          $table->foreign('land_id')->references('land_id')->on('sample_lands')
              ->onUpdate('cascade')->onDelete('cascade');
          $table->decimal('lat', 10, 8);
          $table->decimal('lng', 11, 8);
          $table->decimal('alt',10, 2);
          $table->string('investigator_name',50);
          $table->datetime('investigated_at')->nullable();
          $table->timestamp('uploaded_at')->nullable();
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
