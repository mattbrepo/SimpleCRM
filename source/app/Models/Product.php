<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
  
    public function user_groups()
    {
      return $this->belongsToMany(
        UserGroup::class,
        'products_user_groups',
        'product_id',
        'user_group_id');
    }
}
