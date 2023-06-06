<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'contacts';

  public function user_groups()
  {
    return $this->belongsToMany(
      UserGroup::class,
      'contacts_user_groups',
      'contact_id',
      'user_group_id');
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }    
}
