<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function(){
  // All secure URL's

  //Route::get('testapi3', function () {
  //  return 'ciao';
  //});

  // --- active user (profile)
  Route::get('logout', [UserController::class, 'logout']);
  Route::get('profile', [UserController::class, 'getProfile']);
  Route::post('profile', [UserController::class, 'setProfileData']);
  Route::post('profile/password', [UserController::class, 'setProfilePassword']);
  Route::get('profile/group', [UserController::class, 'getProfileGroup']);

  // --- groups
  Route::get('groups', [UserGroupController::class, 'index']);
  Route::get('group/{id}', [UserGroupController::class, 'show']);
  Route::get('group/{id}/users', [UserGroupController::class, 'getUsers']);
  Route::get('groups/create', [UserGroupController::class, 'create']);
  Route::post('groups', [UserGroupController::class, 'store']);
  Route::post('group/{id}', [UserGroupController::class, 'update']);
  Route::delete('group/{id}', [UserGroupController::class, 'destroy']);
  Route::post('group/{id}/assign-companies', [CompanyController::class, 'setGroupAssign']);
  Route::post('group/{id}/remove-companies', [CompanyController::class, 'setGroupRemove']);
  Route::post('group/{id}/assign-contacts', [ContactController::class, 'setGroupAssign']);
  Route::post('group/{id}/remove-contacts', [ContactController::class, 'setGroupRemove']);
  Route::post('group/{id}/assign-products', [ProductController::class, 'setGroupAssign']);
  Route::post('group/{id}/remove-products', [ProductController::class, 'setGroupRemove']);
  Route::post('group/{id}/assign-orders', [OrderController::class, 'setGroupAssign']);
  Route::post('group/{id}/remove-orders', [OrderController::class, 'setGroupRemove']);
  
  // --- users
  Route::get('users', [UserController::class, 'index']);
  Route::get('user/{id}', [UserController::class, 'show']);
  Route::get('user/{id}/group', [UserController::class, 'getGroup']);
  Route::post('user/{id}', [UserController::class, 'update']);
  Route::post('user/{id}/password', [UserController::class, 'setPassword']);
  Route::get('users/create', [UserController::class, 'create']);
  Route::post('users', [UserController::class, 'store']);
  Route::delete('user/{id}', [UserController::class, 'destroy']);

  // --- products
  Route::get('products', [ProductController::class, 'index']);
  Route::get('product/{id}', [ProductController::class, 'show']);
  Route::get('product/{id}/groups', [ProductController::class, 'getGroups']);
  Route::get('products/create', [ProductController::class, 'create']);
  Route::post('products', [ProductController::class, 'store']);
  Route::post('product/{id}', [ProductController::class, 'update']);
  Route::delete('product/{id}', [ProductController::class, 'destroy']);

  // --- countries
  Route::get('countries', [CountryController::class, 'index']);
  Route::get('country/{id}', [CountryController::class, 'show']);

  // --- companies
  Route::get('companies', [CompanyController::class, 'index']);
  Route::get('company/{id}', [CompanyController::class, 'show']);
  Route::get('company/{id}/groups', [CompanyController::class, 'getGroups']);
  Route::get('companies/create', [CompanyController::class, 'create']);
  Route::post('companies', [CompanyController::class, 'store']);
  Route::post('company/{id}', [CompanyController::class, 'update']);
  Route::delete('company/{id}', [CompanyController::class, 'destroy']);

  // --- contacts
  Route::get('contacts', [ContactController::class, 'index']);
  Route::get('contact/{id}', [ContactController::class, 'show']);
  Route::get('contact/{id}/groups', [ContactController::class, 'getGroups']);
  Route::get('contacts/create', [ContactController::class, 'create']);
  Route::post('contacts', [ContactController::class, 'store']);
  Route::post('contact/{id}', [ContactController::class, 'update']);
  Route::delete('contact/{id}', [ContactController::class, 'destroy']);

  // --- orders
  Route::get('orders', [OrderController::class, 'index']);
  Route::get('order/{id}', [OrderController::class, 'show']);
  Route::get('order/{id}/groups', [OrderController::class, 'getGroups']);
  Route::get('order/{id}/order_products_licenses', [OrderController::class, 'getOrderProducts']);
  //Route::get('orders/create', [OrderController::class, 'create']);
  Route::post('orders', [OrderController::class, 'store']);
  Route::post('order/{id}', [OrderController::class, 'update']);
  Route::delete('order/{id}', [OrderController::class, 'destroy']);

});

Route::post('login', [UserController::class, 'login']);