<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\UserGroup;
use App\Models\User;

class CompanySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::table('companies')->delete();

    $user1 = User::all()->first();
    $company1 = Company::create(['name' => 'Company 1', 'website' => 'http://company1.com', 'country_id' => 2, 'user_id' => $user1->id]);
    $company2 = Company::create(['name' => 'Company 2', 'website' => 'http://company2.com', 'country_id' => 15, 'user_id' => $user1->id]);

    $user_group1 = UserGroup::all()->first();
    $user_group2 = UserGroup::all()->last();

    \DB::table('companies_user_groups')->delete();
    
    $companies_user_groups = [
      ['company_id' => $company1->id, 'user_group_id' => $user_group1->id],
      ['company_id' => $company2->id, 'user_group_id' => $user_group2->id],
      ['company_id' => $company2->id, 'user_group_id' => $user_group1->id] // a company is always associated with the admin group
    ];
    \DB::table('companies_user_groups')->insert($companies_user_groups);
  }
}
