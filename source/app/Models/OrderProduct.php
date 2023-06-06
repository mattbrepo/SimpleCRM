<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'orders_products';

  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function licenses()
  {
    return $this->hasMany(
      License::class,
      'orders_products_id',
      'id');
  }
}
