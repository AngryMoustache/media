<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreInfoToTheAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->integer('height')->after('alt_name')->nullable();
            $table->integer('width')->after('alt_name')->nullable();
            $table->integer('size')->after('alt_name')->nullable();
            $table->string('mime_type')->after('alt_name')->nullable();
            $table->string('extension')->after('alt_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn('height');
            $table->dropColumn('width');
            $table->dropColumn('size');
            $table->dropColumn('mime_type');
            $table->dropColumn('extension');
        });
    }
}
