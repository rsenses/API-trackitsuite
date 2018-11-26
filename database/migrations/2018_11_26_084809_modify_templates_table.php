<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class ModifyTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template', function ($table) {
            $table->string('event');
            $table->integer('product_id')->unsigned();

            $table->foreign('product_id')
                ->references('product_id')
                ->on('product')
                ->onDelete('cascade');
        });

        Schema::table('product', function ($table) {
            $table->dropForeign('product_template_id_foreign');
            $table->dropColumn('template_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template', function ($table) {
            $table->dropForeign('template_product_id_foreign');
            $table->dropColumn('product_id');
            $table->dropColumn('event');
        });

        Schema::table('product', function ($table) {
            $table->integer('template_id')->unsigned()->nullable();

            $table->foreign('template_id')
                ->references('template_id')
                ->on('template')
                ->onDelete('cascade');
        });
    }
}
