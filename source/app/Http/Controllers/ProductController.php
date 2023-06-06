<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserGroup;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    // prodebug
    //error_log('ProductController::index');
    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    return response(Product::all(), 200);
  }

  public function show(Request $request, $id)
  {
    // prodebug
    //error_log('ProductController::show');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    return response(Product::find($id), 200);
  }

  public function update(Request $request, $id)
  {
    // prodebug
    //error_log('ProductController::update');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $product = Product::find($id);
    
    $productCheck = Product::where('name', $request->name)->first();
    if ($productCheck != null && $productCheck != $product) {
      return response([
        'message' => ['Product name already taken.']
      ], 404);        
    }

    $product->name = $request->name;
    $product->software = $request->software;
    $product->major_version = $request->major_version;
    $product->price = $request->price;
    $product->single = $request->single;
    $product->rent = $request->rent;
    $product->academic = $request->academic;
    $product->trial = $request->trial;
    $product->os_windows = $request->os_windows;
    $product->os_linux = $request->os_linux;
    $product->os_mac = $request->os_mac;
    $product->save();

    return response($product, 200);
  }

  public function create(Request $request)
  {
    // prodebug
    //error_log('ProductController::create');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $product = new Product();
    $product->num_licenses = 1;

    return response($product, 200);
  }

  public function store(Request $request)
  {
    // prodebug
    //error_log('ProductController::store');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $productCheck = Product::where('name', $request->name)->first();
    if ($productCheck != null) {
      return response([
        'message' => ['Product name already taken.']
      ], 404);        
    }

    $product = new Product();
    $product->name = $request->name;
    $product->software = $request->software;
    $product->major_version = $request->major_version;
    $product->price = $request->price;
    $product->single = $request->single;
    $product->rent = $request->rent;
    $product->academic = $request->academic;
    $product->trial = $request->trial;
    $product->os_windows = $request->os_windows;
    $product->os_linux = $request->os_linux;
    $product->os_mac = $request->os_mac;
    $product->save();

    return response($product, 200);
  }

  public function destroy(Request $request, $id)
  {
    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $product = Product::find($id);
    $product->delete();

    return response(Product::all(), 200);
  }

  public function getGroups(Request $request, $id)
  {
    // prodebug
    //error_log('ProductController::getGroups');

    $product = $request->user()->group->products->find($id);
    return response($product->user_groups, 200);
  }

  public function setGroupAssign(Request $request, $group_id)
  {
    //prodebug
    //error_log('ProductController::setGroup');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = UserGroup::find($group_id);

    foreach($request->products as $product) {
      $db_product = Product::find($product['id']);
      if (!$db_product) {
        return response([
          'message' => ['Product not found: ' . $product]
        ], 404);        
      }

      $userGroup->products()->syncWithoutDetaching($db_product->id); // sync is better than attach because doesn't try to create duplicate records!
    }
    
    return response(['temp'], 200);
  }

  public function setGroupRemove(Request $request, $group_id)
  {
    //prodebug
    //error_log('ProductController::setGroupRemove');

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

    foreach($request->products as $product) {
      $db_product = Product::find($product['id']);
      if (!$db_product) {
        return response([
          'message' => ['Product not found: ' . $product]
        ], 404);        
      }

      $userGroup->products()->detach($db_product->id);
    }
    
    return response(['temp'], 200);
  }
}