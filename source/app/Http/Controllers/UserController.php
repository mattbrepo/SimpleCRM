<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    // prodebug
    //error_log('UserController::index');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    return response(User::all(), 200);
  }

  public function show(Request $request, $id)
  {
    // prodebug
    //error_log('UserController::show');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    return response(User::find($id), 200);
  }

  public function update(Request $request, $id)
  {
    // prodebug
    //error_log('UserController::update');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $user = User::find($id);
    
    $userCheck = User::where('name', $request->name)->first();
    if ($userCheck != null && $userCheck != $user) {
      return response([
        'message' => ['User name already taken.']
      ], 404);        
    }

    $userCheck = User::where('email', $request->email)->first();
    if ($userCheck != null && $userCheck != $user) {
      return response([
        'message' => ['User email already taken.']
      ], 404);        
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->user_group_id = $request->group_id;
    $user->save();

    return response($user, 200);
  }

  public function create(Request $request)
  {
    // prodebug
    //error_log('UserController::create');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $user = new User();
    $user->user_group_id = UserGroup::all()->last()->id;

    return response($user, 200);
  }

  public function store(Request $request)
  {
    // prodebug
    //error_log('UserController::store');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userCheck = User::where('name', $request->name)->first();
    if ($userCheck != null) {
      return response([
        'message' => ['User name already taken.']
      ], 404);        
    }

    $userCheck = User::where('email', $request->email)->first();
    if ($userCheck != null) {
      return response([
        'message' => ['User email already taken.']
      ], 404);        
    }

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->newPassword);
    $user->user_group_id = $request->group_id;
    $user->save();

    return response($user, 200);
  }

  public function destroy(Request $request, $id)
  {
    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $user = User::find($id);
    $user->delete();

    return response(User::all(), 200);
  }

  // handle user login
  function login(Request $request)
  {
    //error_log('user: ' . $request->username); //prodebug
    $user = User::where('name', $request->username)->first();

    // print_r($data);
    if (!$user || !Hash::check($request->password, $user->password)) {
      //error_log('user not found!!!'); //prodebug
      return response([
        'message' => ['These credentials do not match our records.']
      ], 404);
    }

    //error_log('pre token!!!');
    $token = $user->createToken('my-app-token')->plainTextToken;

    $response = [
      'user' => $user,
      'token' => $token
    ];

    return response($response, 201);
  }

  function logout(Request $request)
  {
    //https://laravel.com/docs/8.x/sanctum#revoking-tokens
    //error_log('logout'); //prodebug
    //error_log();
    return $request->user()->currentAccessToken()->delete();
  }

  function getProfile(Request $request)
  {
    //error_log('getUser'); //prodebug
    //error_log($request);
    return response($request->user(), 200);
  }

  function setProfileData(Request $request)
  {
    //error_log('setUserData'); //prodebug
    //error_log($request->email);
    $request->user()->email = $request->email;
    $request->user()->save();
    return response($request->user(), 200);
  }

  function setProfilePassword(Request $request)
  {
    //error_log('setUserPassword'); //prodebug
    //error_log($request->currentPassword);

    // new password check
    if (strlen($request->newPassword) < 1) {
      return response([
        'message' => 'New password  is invalid! Please try again.'
      ], 400);
    }

    // current password check
    if (!Hash::check($request->currentPassword, $request->user()->password)) {
      return response([
        'message' => 'Current password could not be verified! Please try again.'
      ], 400);
    }

    // hash and save new password
    $request->user()->fill(['password' => Hash::make($request->newPassword)])->save();

    return response($request->user(), 200);
  }
  
  function getProfileGroup(Request $request)
  {
    //error_log('UserController::getProfileGroup'); //prodebug
    //error_log($request->user());
    //error_log($request->user()->group());
    return response($request->user()->group, 200);
  }

  public function getGroup(Request $request, $id)
  {
    // prodebug
    //error_log('UserController::getGroup');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    return response(User::find($id)->group, 200);
  }

  function setPassword(Request $request, $id)
  {
    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    // new password check
    if (strlen($request->newPassword) < 1) {
      return response([
        'message' => 'New password  is invalid! Please try again.'
      ], 400);
    }

    // hash and save new password
    $user = User::find($id);
    $user->fill(['password' => Hash::make($request->newPassword)])->save();

    return response($request->user(), 200);
  }
}
