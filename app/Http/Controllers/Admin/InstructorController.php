<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Grade;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\InstructorRequest;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:country-manager,latam-manager,admin');
    } 
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* 
    public function index(Request $request)
    {
        $search = $request->get('search');
        $paginate = 10;
        
        $model = User::select('*')->whereHas('roles', function($query) {
            $query->where('role_id', 2);
        });
        
        if (!$request->user()->hasAnyRole(['latam-manager', 'admin'])) {
            $countryId = $request->user()->city->country_id;
            
            $model->whereHas('city', function($query) use ($countryId) {
                $query->where('country_id', $countryId);
            });
        }        
        
        if (!empty($search)) {
            $model->where('name', 'like', "%{$search}%");
        }
        
        $instructors = $model->paginate($paginate);
        
        return view('admin.instructors.index', compact('instructors'));
    }
    */
    
    public function index(Request $request)
    {
        return view('admin.instructors.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        
        $model = User::select(['users.*', 'cities.name as city', 'countries.name as country', 'academies.name as academy'])->whereHas('roles', function($query) {
            $query->where('role_id', 2);
        });
        
        $model->join('cities', 'cities.id', 'users.city_id');  
        $model->join('countries', 'countries.id', 'cities.country_id');  
        $model->join('academies', 'academies.id', 'users.academy_id');  
        
        $model->with(['city', 'city.country', 'academy']);        
        
        if (!$request->user()->hasAnyRole(['latam-manager', 'admin'])) {
            $countryId = $request->user()->city->country_id;
            
            $model->whereHas('city', function($query) use ($countryId) {
                $query->where('country_id', $countryId);
            });
        }  
        
        
        $total = $model->count();


        if (is_array($search) && !empty($search['value'])) {
            $model->where(function($query) use ($search) {
                $words = explode(' ', $search['value']);
                
                foreach ($words as $word) {
                    $query->orWhereRaw("concat(' ',name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',last_name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',email) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',phone) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ', (case users.active when 1 then '". __('Yes') ."' when 0 then '". __('No') ."' end)) like '% ". $word ."%'");
                }
            });
        }


        $filtered = $model->count();
        
        if (is_array($order)) {
            foreach ($order as $value) {
                $column = $value['column'];
                $dir = $value['dir'];
                $model->orderBy($columns[$column]['name'], $dir);
            }
        }
        
        $results = $model->offset($start)->limit($length)->get();
       
        $results->map(function ($item, $key) {
            $item->_active = $item->active ? __('Yes') : __('No');
        });       
        
        $dt = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $results,
        ];
        
        return response()->json($dt);        
    }      

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $grades = Grade::all();
        
        return view('admin.instructors.create', compact('roles', 'grades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InstructorRequest $request)
    {
		$validated = $request->validated();
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['role_id'] = 2;
                
        if (!$request->filled('active')) {
            $validated['active'] = false;
        }

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->profile_photo->store('users/profile_photo', 'public');
        }        
        
		$instructor = User::create($validated);
        
        if (!empty($validated['role_id'])) {
            $instructor->roles()->attach(Role::findOrFail($validated['role_id']));
        }        
		
        return redirect()->route('admin.instructors.index')
			->with('status', __('The record has been inserted successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		$instructor = User::find($id);
        $roles = Role::all();
        $grades = Grade::all();
        
        return view('admin.instructors.edit', compact('instructor', 'roles', 'grades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InstructorRequest $request, $id)
    {        
		$validated = $request->validated();
        
        $validated['role_id'] = 2;
        
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        if (!$request->filled('active')) {
            $validated['active'] = false;
        }
        
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->profile_photo->store('users/profile_photo', 'public');
        }
        
        $instructor = User::find($id);
        $instructor->update($validated);
        
        if (empty($validated['role_id'])) {
            $validated['role_id'] = [];
        }
                
        $instructor->roles()->sync(Role::findOrFail($validated['role_id']));        
		
        //return redirect()->route('admin.instructors.index')
        return back()
			->with('status', __('The record has been updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $instructor = User::find($id);
        
        if (\Auth::user()->id == $id) {
            abort(403);
        }

        if ($instructor->roles) {
            $instructor->roles()->detach();
        }

        $instructor->delete();
        
        return redirect()->route('admin.instructors.index')
			->with('status', __('The record has been deleted successfully'));
    }

    /*
    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $instructors = User::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`name`)"), 'like', '% '. join('% ', $words) .'%')
                    ->orWhere(\DB::raw("concat(' ',`last_name`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`name`,' ',`last_name`) AS text"), 'id')
            ->orderBy('name', 'asc')->limit(25);
        
        if ($request->has('with') && $request->with == 'device_token') {
            $instructors->has('deviceTokens');            
        }
        
        if ($request->has('role_id')) {
            // $instructors->whereRoleId($request->role_id);      
            $instructors->with('roles', function($query) use ($request) {
                return $query->where('role_id', $request->role_id);
            });                
        }
            
        $results = $instructors->get();
        
        return response()->json($results);
    }
    */

    public function deleteProfilePhoto()
    {
        return response()->json([]);
    }    
}
