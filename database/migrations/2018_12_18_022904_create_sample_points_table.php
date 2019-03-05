<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('point_id',50);
            $table->string('code',50);
            $table->json('data');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 10, 8);
            $table->decimal('alt',10, 2);
            $table->string('investigator_name',50);
            $table->datetime('investigated_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample_points');
    }
}
