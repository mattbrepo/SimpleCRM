<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\License;
use App\Models\UserGroup;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    // prodebug
    //error_log('OrderController::index');
    return response($request->user()->group->orders, 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    // not really used... (remove also api...)
    //$order = new Order();
    //return response($order, 200);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // prodebug
    error_log('OrderController::store');
    error_log($request);
    
    $requestUserGroup = $request->user()->group;
    if (!$requestUserGroup) {
      return response([
        'message' => ['User group not found.'] // it shouldn't happen
      ], 404);        
    }

    $adminUserGroup = UserGroup::getAdmin();
    if (!$adminUserGroup) {
      return response([
        'message' => ['Admin group not found.'] // it shouldn't happen
      ], 404);        
    }

    $orderCheck = $requestUserGroup->orders->where('name', $request->name)->first();
    if ($orderCheck != null) {
      return response([
        'message' => ['Order name already taken.']
      ], 404);
    }

    if (count($request->order_products) <= 0) {
      return response([
        'message' => ['No product defined.']
      ], 404);
    }

    $company = $request->user()->group->companies->find($request->company_id);
    if ($company == null) {
      return response([
        'message' => ['Company not found.']
      ], 404);
    }

    // create order
    $order = new Order();
    $order->name = $request->name;
    $order->company_id = $company->id;
    $order->user_id = $request->user()->id;
    if ($request->user()->isAdmin()) {
      $order->invoiced = $request->invoiced;
    } else {
      $order->invoiced = false;
    }

    // create orders_products
    $order->total = 0;
    $ops = array();
    $lics = array();
    foreach ($request->order_products as $req_op) {
      $prod = $requestUserGroup->products->find($req_op['prod_id']);
      if ($prod == null) {
        return response([
          'message' => ['Product not found: ' . $req_op['prod_name']]
        ], 404);        
      }

      // create OrderProduct
      $op = new OrderProduct();
      $op->product_id = $prod->id;
      $op->discount = 0;
      if ($request->user()->isAdmin()) {
        $op->discount = floatval($req_op['discount']);
      }
      $op->total = $prod->price * (1 - $op->discount / 100);
      $op->note = $req_op['note'];
      array_push($ops, $op);

      // create Licenses
      for ($i = 0; $i < $prod->num_licenses; $i++) {
        $lic = new License();
        $lic->extra_id = $i;
        array_push($lics, $lic);
      }

      $order->total = $order->total + $op->total;
    }

    // save order and orders_products and licenses
    $order->save();
    $lic_idx = 0;
    foreach ($ops as $op) {
      $op->order_id = $order->id;
      $op->save();

      $prod = $requestUserGroup->products->find($op->product_id);
      for ($i = 0; $i < $prod->num_licenses; $i++) {
        $lic = $lics[$lic_idx];
        $lic->orders_products_id = $op->id;
        $lic->save();
        $lic_idx++;
      }
    }

    // assign it to the user group and to the admin group 
    $adminUserGroup->orders()->syncWithoutDetaching($order->id);
    $requestUserGroup->orders()->syncWithoutDetaching($order->id);

    return response($order, 200);
  }

  public function show(Request $request, $id)
  {
    // prodebug
    //error_log('OrderController::show');
    //error_log($request->user());
    return response($request->user()->group->orders->find($id), 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Order  $order
   * @return \Illuminate\Http\Response
   */
  public function edit(Order $order)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Order  $order
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    // prodebug
    error_log('OrderController::update');
    $requestUserGroup = $request->user()->group;
    if (!$requestUserGroup) {
      return response([
        'message' => ['User group not found.'] // it shouldn't happen
      ], 404);        
    }

    $order = Order::find($id);
    $order->name = $request->name;
    if ($request->user()->isAdmin()) {
      $order->invoiced = $request->invoiced;
    }

    for ($i = 0; $i < $request->order_products; $i++) {
      $product_id = $request[$i . '_product_id'];
      $prod = $requestUserGroup->products->find($product_id);

      for ($j = 0; $j < $prod->num_licenses; $j++) {
        $file = $request->file($i . '_' . $j . '_lic_req_filename');
        $file->storeAs('./myfiles', 'abc.xyz');
        error_log($file->getClientOriginalName());
      }
    }

    $order->save();

    return response($order, 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Order  $order
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $order = Order::find($id);
    $order->delete();

    return index($request);
  }

  public function getGroups(Request $request, $id)
  {
    // prodebug
    //error_log('OrderController::getGroups');

    $order = $request->user()->group->orders->find($id);
    return response($order->user_groups, 200);
  }

  public function setGroupAssign(Request $request, $group_id)
  {
    //prodebug
    //error_log('OrderController::setGroup');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = UserGroup::find($group_id);

    foreach($request->orders as $order) {
      $db_order = Order::find($order['id']);
      if (!$db_order) {
        return response([
          'message' => ['Order not found: ' . $order]
        ], 404);        
      }

      $userGroup->orders()->syncWithoutDetaching($db_order->id); // sync is better than attach because doesn't try to create duplicate records!
    }
    
    return response(['temp'], 200);
  }

  public function setGroupRemove(Request $request, $group_id)
  {
    //prodebug
    //error_log('OrderController::setGroupRemove');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = UserGroup::find($group_id);
    if ($userGroup->admin == 1) {
      return response([
        'message' => ['They cannot be removed from admin group.']
      ], 404);        
    }

    foreach($request->orders as $order) {
      $db_order = Order::find($order['id']);
      if (!$db_order) {
        return response([
          'message' => ['Order not found: ' . $order]
        ], 404);        
      }

      $userGroup->orders()->detach($db_order->id);
    }
    
    return response(['temp'], 200);
  }

  public function getOrderProducts(Request $request, $id)
  {
    // prodebug
    //error_log('OrderController::getOrdersProducts');

    $order = $request->user()->group->orders->find($id);
    $order_prods = $order->order_products;
    $mod_order_prods = array();
    foreach ($order_prods as $op) {
      $prod = $op->product;
      $lics = $op->licenses;
      $mod_order_prods[] = [ 
        'id' => $op->id,
        'discount' => $op->discount,
        'total' => $op->total,
        'note' => $op->note,
        'name' => $prod->name,
        'price' => $prod->price,
        'licenses' => $lics
      ];
    }

    return response($mod_order_prods, 200);
  }
}