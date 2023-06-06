<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'user_groups';

  public function users()
  {
    return $this->hasMany(User::class);
  }

  public static function getAdmin()
  {
    return UserGroup::where('admin', 1)->first();
  }

  public function companies()
  {
    return $this->belongsToMany(
      Company::class,
      'companies_user_groups',
      'user_group_id',
      'company_id');
  }

  public function contacts()
  {
    return $this->belongsToMany(
      Contact::class,
      'contacts_user_groups',
      'user_group_id',
      'contact_id');
  }

  public function products()
  {
    return $this->belongsToMany(
      Product::class,
      'products_user_groups',
      'user_group_id',
      'product_id');
  }

  public function orders()
  {
    return $this->belongsToMany(
      Order::class,
      'orders_user_groups',
      'user_group_id',
      'order_id');
  }
}
