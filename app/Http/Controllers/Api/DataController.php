<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Country;
use App\Models\City;
use App\Models\Academy;
use App\Models\Role;
use App\Models\Grade;

class DataController extends Controller
{
    public function countries(Request $request)
    {
        $countries = Country::orderBy('name')->get();
        return response()->json($countries, 200);
    }
    
    public function cities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|integer|exists:countries,id',
        ]);
        
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }        
        
        $validated = $validator->validated();      
        
        $cities = City::orderBy('name');
        
        if ($request->filled('country_id')) {
            $cities->where('country_id', $request->country_id);
        }
        
        $result = $cities->get();
        
        return response()->json($result, 200);
    }

    public function roles(Request $request)
    {
        $roles = Role::select(['*', 'description as name'])->orderBy('id')->get();
        return response()->json($roles, 200);
    }

    public function grades(Request $request)
    {
        $grades = Grade::orderBy('name')->get();
        return response()->json($grades, 200);
    }

    public function academies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|integer|exists:countries,id',
            'active' => 'nullable',
        ]);
        
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }        
                
        $validated = $validator->validated();
        
        $academies = Academy::orderBy('name');
        
        if ($request->filled('country_id')) {
            $academies->where('country_id', $request->country_id);
        }
        
        if ($request->filled('active')) {
            $academies->where('active', $request->boolean('active'));
        }        
        
        $result = $academies->get();
        
        return response()->json($result, 200);
    }   
}
