<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SafeDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('devices', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('maintenances', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('memos', function (Blueprint $table) {
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('devices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('memos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
