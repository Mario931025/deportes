<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Http\Requests\Admin\CityRequest;

class CityController extends Controller
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
        
        $model = City::select('*');
        
        if ($request->user()->hasRole('country-manager')) {
            $model->where('country_id', $request->user()->city->country_id);
        }        
        
        if (!empty($search)) {
            $model->where('name', 'like', "%{$search}%");
        }
        
        $cities = $model->paginate($paginate);
        
        return view('admin.cities.index', compact('cities'));
    }
    */
    
    public function index(Request $request)
    {
        return view('admin.cities.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        
        $model = City::select(['cities.*', 'c.name as country']);
        $model->join('countries as c', 'c.id', 'cities.country_id');
        
        if ($request->user()->hasRole('country-manager')) {
            $model->where('country_id', $request->user()->city->country_id);
        }
        
        
        $total = $model->count();


        if (is_array($search) && !empty($search['value'])) {
            $model->where(function($query) use ($search) {
                $words = explode(' ', $search['value']);
                
                foreach ($words as $word) {
                    $query->orWhereRaw("concat(' ',cities.name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',c.name) like '% ". $word ."%'");
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
        return view('admin.cities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
		$validated = $request->validated();
        
		City::create($validated);
		
        return redirect()->route('admin.cities.index')
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
		$city = City::find($id);
        
        if (auth()->user()->city->country_id != $city->country_id) {
            abort(403);
        }
        
        return view('admin.cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CityRequest $request, $id)
    {        
		$validated = $request->validated();

        $city = City::find($id);
        
        if (auth()->user()->city->country_id != $city->country_id) {
            abort(403);
        }        
        
        $city->update($validated);
		
        return redirect()->route('admin.cities.index')
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
        $city = City::find($id);
        
        if (auth()->user()->city->country_id != $city->country_id) {
            abort(403);
        }        
        
        $city->delete();
        
        return redirect()->route('admin.cities.index')
			->with('status', __('The record has been deleted successfully'));
    }

    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $cities = City::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`name`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`name`) AS text"), 'id')
            ->orderBy('name', 'asc')->limit(25);
            
        if ($request->has('country_id')) {
            $cities->whereCountryId($request->country_id);
        }
        
        if ($request->has('with') && $request->with == 'country') {
            $cities->has('country');            
        }
            
        $results = $cities->get();
        
        return response()->json($results);
    }  
}
