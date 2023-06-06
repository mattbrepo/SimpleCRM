<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsUserGroupsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('products_user_groups', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('product_id');
      $table->foreign('product_id')
        ->references('id')
        ->on('products')->onDelete('cascade');
      $table->unsignedBigInteger('user_group_id');
      $table->foreign('user_group_id')
        ->references('id')
        ->on('user_groups')->onDelete('cascade');
      $table->unique(['product_id', 'user_group_id']);
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
    Schema::dropIfExists('products_user_groups');
  }
}
