<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'licenses';

  public function order_product()
  {
    return $this->belongsTo(OrderProduct::class, 'orders_products_id');
  }
}
