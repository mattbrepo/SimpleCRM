<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'orders';

  public function user_groups()
  {
    return $this->belongsToMany( // hasMany: https://stackoverflow.com/questions/36208460/hasmany-vs-belongstomany-in-laravel-5-x
      UserGroup::class,
      'orders_user_groups',
      'order_id',
      'user_group_id');
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function order_products()
  {
    return $this->hasMany(
      OrderProduct::class,
      'order_id',
      'id');
  }
}
