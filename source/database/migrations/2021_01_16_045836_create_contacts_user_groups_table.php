<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsUserGroupsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('contacts_user_groups', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('contact_id');
      $table->foreign('contact_id')
        ->references('id')
        ->on('contacts')->onDelete('cascade');
      $table->unsignedBigInteger('user_group_id');
      $table->foreign('user_group_id')
        ->references('id')
        ->on('user_groups')->onDelete('cascade');
      $table->unique(['contact_id', 'user_group_id']);
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
    Schema::dropIfExists('contacts_user_groups');
  }
}
