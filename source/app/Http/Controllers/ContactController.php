<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\UserGroup;
use Illuminate\Http\Request;

class ContactController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    // prodebug
    //error_log('ContactController::index');

    // gets only the contacts associated with the user_group of the current user
    // AND of companies associated with the user_group of the current user
    $contacts = Contact::select('contacts.*')->
                join('companies', 'contacts.company_id', '=', 'companies.id')->
                join('contacts_user_groups', 'contacts_user_groups.contact_id', '=', 'contacts.id')->
                where('contacts_user_groups.user_group_id', '=', $request->user()->group->id)->
                join('companies_user_groups', 'companies_user_groups.company_id', '=', 'companies.id')->
                where('companies_user_groups.user_group_id', '=', $request->user()->group->id)->
                get();

    return response($contacts, 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $contact = new Contact();
    return response($contact, 200);
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
    //error_log('ContactController::store');
    
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

    $contact = new Contact();
    $contact->title = $request->title;
    $contact->first_name = $request->first_name;
    $contact->last_name = $request->last_name;
    $contact->email = $request->email;
    $contact->company_id = $request->company_id;
    $contact->note = $request->note;
    $contact->source = $request->source;
    $contact->site_user = $request->site_user;
    $contact->user_id = $request->user()->id;
    $contact->save();

    // assign it to the user group and to the admin group 
    $adminUserGroup->contacts()->syncWithoutDetaching($contact->id);
    $requestUserGroup->contacts()->syncWithoutDetaching($contact->id);

    return response($contact, 200);
  }

  public function show(Request $request, $id)
  {
    // prodebug
    error_log('ContactController::show');
    error_log($request->user()->group->contacts);
    return response($request->user()->group->contacts->find($id), 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Contact  $contact
   * @return \Illuminate\Http\Response
   */
  public function edit(Contact $contact)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $contact = $request->user()->group->contacts->find($id);
    if (!$contact) {
      return response([
        'message' => [ 'Contact not found' ] 
      ], 400);
    }

    $contact->title = $request->title;
    $contact->first_name = $request->first_name;
    $contact->last_name = $request->last_name;
    $contact->email = $request->email;
    $contact->company_id = $request->company_id;
    $contact->note = $request->note;
    $contact->source = $request->source;
    $contact->site_user = $request->site_user;
    $contact->save();

    return response($contact, 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $contact = Contact::find($id); // only admin can delete!

    $contact->delete();

    return index($request);
  }

  public function getGroups(Request $request, $id)
  {
    // prodebug
    //error_log('ContactController::getGroups');

    $contact = $request->user()->group->contacts->find($id);
    return response($contact->user_groups, 200);
  }

  public function setGroupAssign(Request $request, $group_id)
  {
    //prodebug
    //error_log('ContactController::setGroup');

    if (!$request->user()->isAdmin()) {
      return response([
        'message' => ['Not authorized.']
      ], 404);        
    }

    $userGroup = UserGroup::find($group_id);

    foreach($request->contacts as $contact) {
      $db_contact = Contact::find($contact['id']);
      if (!$db_contact) {
        return response([
          'message' => ['Contact not found: ' . $contact]
        ], 404);        
      }

      $userGroup->contacts()->syncWithoutDetaching($db_contact->id); // sync is better than attach because doesn't try to create duplicate records!
    }
    
    return response(['temp'], 200);
  }

  public function setGroupRemove(Request $request, $group_id)
  {
    //prodebug
    //error_log('ContactController::setGroupRemove');

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

    foreach($request->contacts as $contact) {
      $db_contact = Contact::find($contact['id']);
      if (!$db_contact) {
        return response([
          'message' => ['Contact not found: ' . $contact]
        ], 404);        
      }

      $userGroup->contacts()->detach($db_contact->id);
    }
    
    return response(['temp'], 200);
  }
}