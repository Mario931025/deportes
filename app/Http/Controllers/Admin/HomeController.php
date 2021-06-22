<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $academies = \App\Models\Academy::count();
        
        $students = \App\Models\User::whereHas('roles', function($query) {
            $query->where('id', 1);
        })->count();
        
        $instructors = \App\Models\User::whereHas('roles', function($query) {
            $query->where('id', 2);
        })->count();
        
        return view('admin.home', compact('academies','students','instructors'));
    }
}
