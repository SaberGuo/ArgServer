<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlotsRelationshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plots_rels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plot_id')->unsigned();
            $table->integer('owner_id')->unsigned();

            $table->foreign('plot_id')->references('id')->on('plots')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('plots')
                ->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('plots_rels');
    }
}
