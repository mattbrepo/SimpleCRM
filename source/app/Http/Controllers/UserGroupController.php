<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    // prodebug
    //error_log('UserGroupController::index');
    //error_log($request->user());

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    return response(UserGroup::all(), 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    // prodebug
    //error_log('UserGroupController::create');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = new UserGroup();
    $userGroup->admin = false;    
    
    return response($userGroup, 200);
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
    //error_log('UserGroupController::create');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = new UserGroup();
    $userGroupCheck = UserGroup::where('name', $request->name)->first();
    if ($userGroupCheck != null) {
      return response([
        'message' => ['Group name already taken.']
      ], 404);        
    }

    $userGroup->name = $request->name;
    // there is only one admin group and it must be defined manually in the DB
    $userGroup->admin = false;
    $userGroup->save();
    
    return response($userGroup, 200);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\UserGroup  $userGroup
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    // prodebug
    //error_log('UserGroupController::show');
    //error_log($request->user());

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    return response(UserGroup::find($id), 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\UserGroup  $userGroup
   * @return \Illuminate\Http\Response
   */
  public function edit(UserGroup $userGroup)
  {
      //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\UserGroup  $userGroup
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    // prodebug
    //error_log('UserGroupController::update');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = UserGroup::find($id);

    $userGroupCheck = UserGroup::where('name', $request->name)->first();
    if ($userGroupCheck != null && $userGroupCheck != $userGroup) {
      return response([
        'message' => ['Group name already taken.']
      ], 404);        
    }

    $userGroup->name = $request->name;
    // there is only one admin group and it must be defined manually in the DB
    //$userGroup->admin = $request->admin;
    $userGroup->save();

    return response($userGroup, 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\UserGroup  $userGroup
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    // prodebug
    //error_log('UserGroupController::destroy');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = UserGroup::find($id);
    if ($userGroup->users()->count() > 0) {
      return response([
        'message' => ['A group with associated user cannot be deleted.']
      ], 404);        
    } 

    $userGroup->delete();

    return response(UserGroup::all(), 200);
  }

  public function getUsers(Request $request, $id)
  {
    // prodebug
    //error_log('UserGroupController::getUsers');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = UserGroup::find($id);
    
    return response($userGroup->users, 200);
  }

}
