<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Relationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('status_id')->references('id')->on('statuses');
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('device_id')->references('id')->on('devices');
        });

        Schema::table('memos', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('maintenance_id')->references('id')->on('maintenances');
        });

        Schema::table('memo_images', function (Blueprint $table) {
            $table->foreign('memo_id')->references('id')->on('memos');
        });

        Schema::table('maintenance_images', function (Blueprint $table) {
            $table->foreign('maintenance_id')->references('id')->on('maintenances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
