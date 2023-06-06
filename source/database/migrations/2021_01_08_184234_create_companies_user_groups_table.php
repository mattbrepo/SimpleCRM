<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesUserGroupsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('companies_user_groups', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('company_id');
      $table->foreign('company_id')
        ->references('id')
        ->on('companies')->onDelete('cascade');
      $table->unsignedBigInteger('user_group_id');
      $table->foreign('user_group_id')
        ->references('id')
        ->on('user_groups')->onDelete('cascade');
      $table->unique(['company_id', 'user_group_id']);
      $table->timestamps();
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
    Schema::dropIfExists('companies_user_groups');
  }
}
