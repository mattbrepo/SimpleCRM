<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('software');
      $table->string('major_version');
      $table->decimal('price');
      $table->boolean('single');
      $table->boolean('rent');
      $table->boolean('academic');
      $table->boolean('trial');
      $table->boolean('os_windows');
      $table->boolean('os_linux');
      $table->boolean('os_mac');
      $table->integer('num_licenses');
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
    Schema::dropIfExists('products');
  }
}
