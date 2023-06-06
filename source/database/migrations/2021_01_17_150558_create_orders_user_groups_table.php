<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersUserGroupsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('orders_user_groups', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->foreign('order_id')
        ->references('id')
        ->on('orders')->onDelete('cascade');
      $table->unsignedBigInteger('user_group_id');
      $table->foreign('user_group_id')
        ->references('id')
        ->on('user_groups')->onDelete('cascade');
      $table->unique(['order_id', 'user_group_id']);
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
    Schema::dropIfExists('orders_user_groups');
  }
}
