<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Academy;
use App\Http\Requests\Admin\AcademyRequest;

class AcademyController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:country-manager,latam-manager,admin')->except('filter');
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
        
        $model = Academy::select('*');
        
        if ($request->user()->hasRole('country-manager')) {
            $model->where('country_id', $request->user()->city->country_id);
        }        
        
        if (!empty($search)) {
            $model->where('name', 'like', "%{$search}%");
        }
        
        $academies = $model->paginate($paginate);
        
        return view('admin.academies.index', compact('academies'));
    }
    */
    
    public function index(Request $request)
    {
        return view('admin.academies.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        
        $model = Academy::select(['academies.*', 'c.name as country']);
        $model->join('countries as c', 'c.id', 'academies.country_id');        
        
        if ($request->user()->hasRole('country-manager')) {
            $model->where('country_id', $request->user()->city->country_id);
        }        
        
        $total = $model->count();


        if (is_array($search) && !empty($search['value'])) {
            $model->where(function($query) use ($search) {
                $words = explode(' ', $search['value']);

                foreach ($words as $word) {
                    $query->orWhereRaw("concat(' ',academies.name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',c.name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ', (case academies.active when 1 then '". __('Yes') ."' when 0 then '". __('No') ."' end)) like '% ". $word ."%'");
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
        return view('admin.academies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademyRequest $request)
    {
		$validated = $request->validated();
                
        if (!$request->filled('active')) {
            $validated['active'] = false;
        }
        
		Academy::create($validated);
		
        return redirect()->route('admin.academies.index')
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
		$academy = Academy::find($id);
        
        if (!auth()->user()->hasAnyRole(['latam-manager', 'admin'])) {
            if (auth()->user()->city->country_id != $academy->country_id) {
                abort(403);
            }
        }
        
        return view('admin.academies.edit', compact('academy'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AcademyRequest $request, $id)
    {        
		$validated = $request->validated();
                
        if (!$request->filled('active')) {
            $validated['active'] = false;
        }      
        
        $academy = Academy::find($id);
        
        if (!auth()->user()->hasAnyRole(['latam-manager', 'admin'])) {
            if (auth()->user()->city->country_id != $academy->country_id) {
                abort(403);
            }        
        }
        
        $academy->update($validated);
		
        return redirect()->route('admin.academies.index')
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
        $academy = Academy::find($id);
        
        if (!auth()->user()->hasAnyRole(['latam-manager', 'admin'])) {
            if (auth()->user()->city->country_id != $academy->country_id) {
                abort(403);
            }
        }            
        
        $academy->delete();
        
        return redirect()->route('admin.academies.index')
			->with('status', __('The record has been deleted successfully'));
    }

    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $academies = Academy::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`name`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`name`) AS text"), 'id')
            ->orderBy('name', 'asc')->limit(25);
            
        if ($request->filled('country_id')) {
            $academies->where('country_id', $request->country_id);              
        } 

        if ($request->filled('city_id')) {
            $academies->where('city_id', $request->city_id);              
        }         
            
        $results = $academies->get();
        
        return response()->json($results);
    }
}
