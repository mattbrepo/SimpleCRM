<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('licenses', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('orders_products_id');
      $table->unsignedBigInteger('extra_id');
      $table->unsignedBigInteger('contact_id')->nullable();
      $table->dateTime('start_date')->nullable();
      $table->dateTime('end_date')->nullable();
      $table->string('lic_req_filename')->nullable();
      $table->string('lic_req_os')->nullable();
      $table->string('lic_filename')->nullable();
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
    Schema::dropIfExists('licenses');
  }
}
