<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Requests\Admin\GradeRequest;

class RoleController extends Controller
{
    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $roles = Role::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`description`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`description`) AS text"), 'id')
            ->orderBy('id', 'asc')
            ->limit(25);
            
        $results = $roles->get();
        
        return response()->json($results);
    }
}
