<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\UserGroup;

class ProductSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::table('products')->delete();

    $user_group1 = UserGroup::all()->first();
    $user_group2 = UserGroup::all()->last();

    $product1 = Product::create(['name' => 'Product 1', 'software' => 'product 1', 'major_version' => '1', 'price' => 400, 'single' => true, 'rent' => true, 'academic' => true, 'trial' => false, 'os_windows' => true, 'os_linux' => true, 'os_mac' => true, 'num_licenses' => 1]);
    $product2 = Product::create(['name' => 'Product 2', 'software' => 'product 2', 'major_version' => '1', 'price' => 400, 'single' => true, 'rent' => false, 'academic' => true, 'trial' => false, 'os_windows' => true, 'os_linux' => true, 'os_mac' => true, 'num_licenses' => 1]);
    $product3 = Product::create(['name' => 'Product 3', 'software' => 'product 3', 'major_version' => '2', 'price' => 800, 'single' => true, 'rent' => false, 'academic' => true, 'trial' => false, 'os_windows' => true, 'os_linux' => true, 'os_mac' => true, 'num_licenses' => 3]);
   
    \DB::table('products_user_groups')->delete();
    
    $products_user_groups = [
      ['product_id' => $product1->id, 'user_group_id' => $user_group1->id],
      ['product_id' => $product2->id, 'user_group_id' => $user_group1->id], // a contact is always associated with the admin group
      ['product_id' => $product2->id, 'user_group_id' => $user_group2->id],
      ['product_id' => $product3->id, 'user_group_id' => $user_group1->id], // a contact is always associated with the admin group
      ['product_id' => $product3->id, 'user_group_id' => $user_group2->id]
    ];
    \DB::table('products_user_groups')->insert($products_user_groups);
  }
}
