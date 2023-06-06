<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'companies';

  public function user_groups()
  {
    return $this->belongsToMany(
      UserGroup::class,
      'companies_user_groups',
      'company_id',
      'user_group_id');
  }

  public function contacts()
  {
    return $this->hasMany(Contact::class);
  }  
}
