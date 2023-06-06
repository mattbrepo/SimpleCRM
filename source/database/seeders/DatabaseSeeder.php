<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserGroup;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // --- user/groups
    \DB::table('user_groups')->delete();
    $useGroup1 = UserGroup::create(['name' => 'MyCompany', 'admin' => 1]);
    $useGroup2 = UserGroup::create(['name' => 'group2', 'admin' => 0]);

    \DB::table('users')->delete();
    User::create(['name' => 'a', 'password' => Hash::make('a'), 'email' => 'matteo@mycompany.com', 'user_group_id' => $useGroup1->id]);
    User::create(['name' => 'b', 'password' => Hash::make('b'), 'email' => 'test@test.com', 'user_group_id' => $useGroup2->id]);

    // --- rest of the DB
    $this->call([
      CountrySeeder::class,
      CompanySeeder::class,
      ContactSeeder::class,
      ProductSeeder::class,
      OrderSeeder::class
    ]);
  }
}
