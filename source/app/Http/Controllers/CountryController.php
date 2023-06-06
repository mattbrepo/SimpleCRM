<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
  public function index()
  {
    return response(Country::all(), 200);
  }

  public function show(Request $request, $id)
  {
    return response(Country::find($id), 200);
  }  
}
