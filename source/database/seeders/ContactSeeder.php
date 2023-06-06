<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use App\Models\UserGroup;

class ContactSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::table('contacts')->delete();

    $user1 = User::all()->first();
    $user2 = User::all()->last();
    $company1 = Company::all()->first();
    $company2 = Company::all()->last();
    $user_group1 = UserGroup::all()->first();
    $user_group2 = UserGroup::all()->last();

    $contact1 = Contact::create(['first_name' => 'One', 'last_name' => 'Johnson', 'company_id' => $company1->id, 'user_id' => $user1->id]);
    $contact2 = Contact::create(['first_name' => 'Two', 'last_name' => 'Johnson', 'company_id' => $company2->id, 'user_id' => $user1->id]);
    $contact3 = Contact::create(['first_name' => 'Three', 'last_name' => 'Johnson', 'company_id' => $company2->id, 'user_id' => $user2->id]);
   
    \DB::table('contacts_user_groups')->delete();
    
    $contacts_user_groups = [
      ['contact_id' => $contact1->id, 'user_group_id' => $user_group1->id],
      ['contact_id' => $contact2->id, 'user_group_id' => $user_group1->id],
      ['contact_id' => $contact3->id, 'user_group_id' => $user_group2->id],
      ['contact_id' => $contact3->id, 'user_group_id' => $user_group1->id] // a contact is always associated with the admin group
    ];
    \DB::table('contacts_user_groups')->insert($contacts_user_groups);
    
  }
}
