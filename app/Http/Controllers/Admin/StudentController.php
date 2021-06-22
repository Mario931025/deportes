<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Grade;
use App\Models\Assistance;
use App\Models\Promotion;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\StudentRequest;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:instructor,country-manager,latam-manager,admin');
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
            $query->where('role_id', 1);
        });
        
        if (!$request->user()->hasAnyRole(['country-manager', 'latam-manager', 'admin'])) {
            $model->whereAcademyId($request->user()->academy_id);
        } elseif (!$request->user()->hasAnyRole(['latam-manager', 'admin'])) {
            $countryId = $request->user()->city->country_id;
            
            $model->whereHas('city', function($query) use ($countryId) {
                $query->where('country_id', $countryId);
            });
        }
        
        
        if (!empty($search)) {
            $model->where('name', 'like', "%{$search}%");
        }
        
        $students = $model->paginate($paginate);
        
        return view('admin.students.index', compact('students'));
    }
    */
    
    public function index(Request $request)
    {
        return view('admin.students.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        $request->merge($search);
                
        $model = $this->data($request);        
        
        /*
        $model = User::select(['users.*', 'cities.name as city', 'countries.name as country', 'academies.name as academy'])->whereHas('roles', function($query) {
            $query->where('role_id', 1);
        });
        
        $model->join('cities', 'cities.id', 'users.city_id');  
        $model->join('countries', 'countries.id', 'cities.country_id');  
        $model->join('academies', 'academies.id', 'users.academy_id');  
        
        $model->with(['city', 'city.country', 'academy']);

        if (!$request->user()->hasAnyRole(['country-manager', 'latam-manager', 'admin'])) {
            $model->whereAcademyId($request->user()->academy_id);
        } elseif (!$request->user()->hasAnyRole(['latam-manager', 'admin'])) {
            $countryId = $request->user()->city->country_id;
            
            $model->whereHas('city', function($query) use ($countryId) {
                $query->where('country_id', $countryId);
            });
        }
        */
        
        $total = $model->count();

        /*
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
        */

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


    protected function data(Request $request)
    {
        //$model = Assistance::select(['assistances.*']);
        
        $model = User::select(['users.*', 'cities.name as city', 'countries.name as country', 'academies.name as academy'])->whereHas('roles', function($query) {
            $query->where('role_id', 1);
        });
        
        $model->join('cities', 'cities.id', 'users.city_id');  
        $model->join('countries', 'countries.id', 'cities.country_id');  
        $model->join('academies', 'academies.id', 'users.academy_id');  
        
        $model->with(['city', 'city.country', 'academy']);
            
        if ($request->filled('value')) {
            $model->where(function($query) {
                $words = explode(' ', request('value'));
                
                foreach ($words as $word) {
                    $query->orWhereRaw("concat(' ',users.name,' ',users.last_name) like '% ". $word ."%'");
                }
            });
        }            
        
        if (!$request->user()->hasAnyRole(['latam-manager','admin'])) {
            $request->merge([
                'country_id' => $request->user()->city->country_id,
            ]);
            
            if (!$request->user()->hasRole('country-manager')) {
                $request->merge([
                    'city_id' => $request->user()->city_id,
                ]);
                
                $request->merge([
                    'academy_id' => $request->user()->academy_id,
                ]);
            }
        }
        
        if ($request->filled('country_id')) {
            $model->whereHas('academy', function($query) {
                $query->where('cities.country_id', request('country_id'));
            });

            if ($request->filled('city_id')) {
                $model->where('users.city_id', $request->city_id);
                
                if ($request->filled('academy_id')) {
                    $model->where('users.academy_id', $request->academy_id);
                }                    
            }            
        }
            
        return $model;
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
        
        return view('admin.students.create', compact('roles', 'grades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudentRequest $request)
    {
		$validated = $request->validated();
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['role_id'] = 1;
                
        if (!$request->filled('active')) {
            $validated['active'] = false;
        }

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->profile_photo->store('users/profile_photo', 'public');
        }        
        
		$student = User::create($validated);
        
        if (!empty($validated['role_id'])) {
            $student->roles()->attach(Role::findOrFail($validated['role_id']));
        }        
		
        return redirect()->route('admin.students.index')
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
		$student = User::find($id);
        $roles = Role::all();
        $grades = Grade::all();
        
        return view('admin.students.edit', compact('student', 'roles', 'grades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StudentRequest $request, $id)
    {
        
		$validated = $request->validated();
        
        $validated['role_id'] = 1;
        
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
        
        $student = User::find($id);
        $student->update($validated);
        
        if (empty($validated['role_id'])) {
            $validated['role_id'] = [];
        }
                
        $student->roles()->sync(Role::findOrFail($validated['role_id']));        
		
        //return redirect()->route('admin.students.index')
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
        $student = User::find($id);

        if (\Auth::user()->id == $id) {
            abort(403);
        }

        if ($student->roles) {
            $student->roles()->detach();
        }

        $student->delete();
        
        return redirect()->route('admin.students.index')
			->with('status', __('The record has been deleted successfully'));
    }

    /*
    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $students = User::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`name`)"), 'like', '% '. join('% ', $words) .'%')
                    ->orWhere(\DB::raw("concat(' ',`last_name`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`name`,' ',`last_name`) AS text"), 'id')
            ->orderBy('name', 'asc')->limit(25);
        
        if ($request->has('with') && $request->with == 'device_token') {
            $students->has('deviceTokens');            
        }
        
        if ($request->has('role_id')) {
            // $students->whereRoleId($request->role_id);      
            $students->with('roles', function($query) use ($request) {
                return $query->where('role_id', $request->role_id);
            });                
        }
            
        $results = $students->get();
        
        return response()->json($results);
    }
    */

    public function deleteProfilePhoto()
    {
        return response()->json([]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function assistances($studentUserId, Request $request)
    {
        $search = $request->get('search');
        $paginate = 10;
        
        $model = Assistance::select('*')->where('student_user_id', $studentUserId);
                
        if (!empty($search)) {
            //$model->where('name', 'like', "%{$search}%");
        }
        
        $assistances = $model->orderBy('created_at', 'desc')->paginate($paginate);
        
        $student = User::find($studentUserId);

        return view('admin.students.assistances.index', compact('student', 'assistances'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function promotions($studentUserId, Request $request)
    {
        $search = $request->get('search');
        $paginate = 10;
        
        $model = Promotion::select('*')->where('student_user_id', $studentUserId);
        
        if (!empty($search)) {
            //$model->where('name', 'like', "%{$search}%");
        }
        
        $promotions = $model->orderBy('created_at', 'desc')->paginate($paginate);
        
        $student = User::find($studentUserId);
        
        return view('admin.students.promotions.index', compact('student', 'promotions'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createPromotion($studentUserId)
    {
        $student = User::find($studentUserId);

        return view('admin.students.promotions.create', compact('student'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePromotion($studentUserId, Request $request)
    {
        if ($request->user()->hasRole('instructor')) {
            $request->merge([
                'instructor_user_id' => $request->user()->id,
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'instructor_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->withErrors($validator)->withInput();
        }
        
        $validated = $validator->validated();
        
        $student = User::find($studentUserId);
        $student->promotions()->save(new Promotion($validated));
        
        $student->grade_id = $validated['grade_id'];
        $student->save();          
		
        return redirect()->route('admin.students.promotions', $studentUserId)
			->with('status', __('The record has been inserted successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyPromotion($studentUserId, $id)
    {
        $promotion = Promotion::find($id);
        $promotion->delete();
        
        return redirect()->route('admin.students.promotions', $studentUserId)
			->with('status', __('The record has been deleted successfully'));
    }        
}
