<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('contacts', function (Blueprint $table) {
      $table->id();
      $table->string('title')->nullable();
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email')->nullable();
      $table->unsignedBigInteger('company_id');
      $table->string('note')->nullable();
      $table->string('source')->nullable();
      $table->string('site_user')->nullable();
      $table->unsignedBigInteger('user_id');
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
    Schema::dropIfExists('contacts');
  }
}
