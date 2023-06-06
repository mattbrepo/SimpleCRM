<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\UserGroup;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\License;

class OrderSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::table('orders')->delete();

    $user1 = User::all()->first();
    $user2 = User::all()->last();
    $company1 = Company::all()->first();
    $company2 = Company::all()->last();
    $user_group1 = UserGroup::all()->first();
    $user_group2 = UserGroup::all()->last();
    $product1 = Product::all()->first();

    $order1 = Order::create(['name' => 'Order 1', 'total' => $product1->price, 'invoiced' => false, 'company_id' => $company1->id, 'user_id' => $user1->id]);
    $orderProd1 = OrderProduct::create(['order_id' => $order1->id, 'product_id' => $product1->id, 'discount' => 0, 'total' => $product1->price]);
    $license1 = License::create(['orders_products_id' => $orderProd1->id, 'extra_id' => 0 ]);
   
    \DB::table('orders_user_groups')->delete();
    
    $orders_user_groups = [
      ['order_id' => $order1->id, 'user_group_id' => $user_group1->id],
      ['order_id' => $order1->id, 'user_group_id' => $user_group2->id]
    ];
    \DB::table('orders_user_groups')->insert($orders_user_groups);

  }
}
