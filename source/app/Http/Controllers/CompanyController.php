<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    // prodebug
    //error_log('CompanyController::index');
    return response($request->user()->group->companies, 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $company = new Company();
    return response($company, 200);
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
    //error_log('CompanyController::store');

    // --- validation
    $validator = Validator::make($request->all(), [
      'name' => ['bail', 'required', 'max:255'],
      'website' => ['max:255'],
      'note' => ['max:255']
    ], getValidatorMessages());

    if($validator->fails()) {
      $errmsg = getValidatorErrorMessage($validator);
      return response([
        'message' => [ $errmsg ] 
      ], 400);
    }

    // --- other test
    $requestUserGroup = $request->user()->group;
    if (!$requestUserGroup) {
      return response([
        'message' => ['User group not found.'] // it shouldn't happen
      ], 400);        
    }

    $adminUserGroup = UserGroup::getAdmin();
    if (!$adminUserGroup) {
      return response([
        'message' => ['Admin group not found.'] // it shouldn't happen
      ], 400);        
    }

    $companyCheck = $requestUserGroup->companies->where('name', $request->name)->first();
    if ($companyCheck != null) {
      return response([
        'message' => ['Company name already taken.']
      ], 400);        
    }
    
    // --- store
    $company = new Company();
    $company->name = $request->name;
    $company->website = $request->website;
    $company->note = $request->note;
    if ($request->country_id >= 0) {
      $company->country_id = $request->country_id;
    }
    $company->user_id = $request->user()->id;
    $company->save();

    // --- assign it to the user group and to the admin group 
    $adminUserGroup->companies()->syncWithoutDetaching($company->id);
    $requestUserGroup->companies()->syncWithoutDetaching($company->id);

    // --- response
    return response($company, 200);
  }

  public function show(Request $request, $id)
  {
    // prodebug
    //error_log('CompanyController::show');
    //error_log($request->user());
    return response($request->user()->group->companies->find($id), 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Http\Response
   */
  public function edit(Company $company)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    // --- validation
    $validator = Validator::make($request->all(), [
      'name' => ['bail', 'required', 'max:255'],
      'website' => ['max:255'],
      'note' => ['max:255']
    ], getValidatorMessages());

    if($validator->fails()) {
      $errmsg = getValidatorErrorMessage($validator);
      return response([
        'message' => [ $errmsg ] 
      ], 400);
    }

    $company = $request->user()->group->companies->find($id);
    if (!$company) {
      return response([
        'message' => [ 'Company not found' ] 
      ], 400);
    }

    $company->name = $request->name;
    $company->website = $request->website;
    $company->note = $request->note;
    if ($request->country_id >= 0) {
      $company->country_id = $request->country_id;
    }
    // user_id is set only when the company is created
    $company->save();

    return response($company, 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Company  $company
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 400);        
    }

    $company = Company::find($id); // only admin can delete!
    
    $company->delete();

    return index($request);
  }

  public function getGroups(Request $request, $id)
  {
    // prodebug
    //error_log('CompanyController::getGroups');

    $company = $request->user()->group->companies->find($id);
    return response($company->user_groups, 200);
  }

  public function setGroupAssign(Request $request, $group_id)
  {
    //prodebug
    //error_log('CompanyController::setGroup');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = UserGroup::find($group_id);

    foreach($request->companies as $company) {
      $db_company = Company::find($company['id']);
      if (!$db_company) {
        return response([
          'message' => ['Company not found: ' . $company]
        ], 404);        
      }

      $userGroup->companies()->syncWithoutDetaching($db_company->id); // sync is better than attach because doesn't try to create duplicate records!
    }
    
    return response(['temp'], 200);
  }

  public function setGroupRemove(Request $request, $group_id)
  {
    //prodebug
    //error_log('CompanyController::setGroupRemove');

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

    foreach($request->companies as $company) {
      $db_company = Company::find($company['id']);
      if (!$db_company) {
        return response([
          'message' => ['Company not found: ' . $company]
        ], 404);        
      }

      $userGroup->companies()->detach($db_company->id);
    }
    
    return response(['temp'], 200);
  }
}
