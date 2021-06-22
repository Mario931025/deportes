<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Grade;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\UserRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('filter');
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
        
        $model = User::select('*');
        
        if (!empty($search)) {
            $model->where('name', 'like', "%{$search}%");
        }
        
        $users = $model->paginate($paginate);
        
        return view('admin.users.index', compact('users'));
    }
    */
    
    public function index(Request $request)
    {
        return view('admin.users.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        $roleQuery = "(select trim(group_concat(concat(' ',r.description))) from role_user as ru join roles as r on ru.role_id=r.id where ru.user_id=users.id)";
        $model = User::select(['users.*', 'cities.name as city', 'countries.name as country', 'academies.name as academy', \DB::raw("{$roleQuery} as roles")]);
        
        $model->join('cities', 'cities.id', 'users.city_id');  
        $model->join('countries', 'countries.id', 'cities.country_id');  
        $model->join('academies', 'academies.id', 'users.academy_id');  
        
        $model->with(['city', 'city.country', 'academy']);        
        
        $total = $model->count();


        if (is_array($search) && !empty($search['value'])) {
            $model->where(function($query) use ($search, $roleQuery) {
                $words = explode(' ', $search['value']);
                
                foreach ($words as $word) {
                    $query->orWhereRaw("concat(' ',users.name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',users.last_name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',users.email) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',users.phone) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',{$roleQuery}) like '% ". $word ."%'")
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
        
        return view('admin.users.create', compact('roles', 'grades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
		$validated = $request->validated();
        
        $validated['password'] = Hash::make($validated['password']);
        
        if (!$request->filled('active')) {
            $validated['active'] = false;
        }

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->profile_photo->store('users/profile_photo', 'public');
        }        
        
		$user = User::create($validated);
        
        if (!empty($validated['role_id'])) {
            $user->roles()->attach(Role::findOrFail($validated['role_id']));
        }        
		
        return redirect()->route('admin.users.index')
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
		$user = User::find($id);
        $roles = Role::all();
        $grades = Grade::all();
        
        return view('admin.users.edit', compact('user', 'roles', 'grades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {        
		$validated = $request->validated();
        
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
        
        $user = User::find($id);
        $user->update($validated);
        
        if (empty($validated['role_id'])) {
            $validated['role_id'] = [];
        }
                
        $user->roles()->sync(Role::findOrFail($validated['role_id']));        
		
        //return redirect()->route('admin.users.index')
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
        $user = User::find($id);

        if (\Auth::user()->id == $id) {
            abort(403);
        }

        if ($user->roles) {
            $user->roles()->detach();
        }

        $user->delete();
        
        return redirect()->route('admin.users.index')
			->with('status', __('The record has been deleted successfully'));
    }

    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $users = User::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`name`)"), 'like', '% '. join('% ', $words) .'%')
                    ->orWhere(\DB::raw("concat(' ',`last_name`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`name`,' ',`last_name`) AS text"), 'id')
            ->orderBy('name', 'asc')->limit(25);
        
        if ($request->has('with') && $request->with == 'device_token') {
            $users->has('deviceTokens');            
        }
        
        if ($request->has('role_id')) {
            // $users->whereRoleId($request->role_id);      
            $users->whereHas('roles', function($query) use ($request) {
                $query->where('role_id', $request->role_id);
            });                
        }
            
        $results = $users->get();
        
        return response()->json($results);
    }

    public function deleteProfilePhoto()
    {
        return response()->json([]);
    }    
}
