<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;

class TermsController extends Controller
{
        
    public function index(Request $request)
    {
        return view('admin.terms');
    }    
    
}
