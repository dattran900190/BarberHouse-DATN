<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('branch_id')->nullable()->index('branch_id')->after('id');
            $table->foreign('branch_id', 'users_branch_id_foreign')->references('id')->on('branches')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_branch_id_foreign');
            $table->dropColumn('branch_id');
        });
    }
}