<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Http\Requests\Admin\CountryRequest;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:latam-manager,admin')->except('filter');
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
        
        $model = Country::select('*');
        
        if (!empty($search)) {
            $model->where('name', 'like', "%{$search}%");
        }
        
        $countries = $model->paginate($paginate);
        
        return view('admin.countries.index', compact('countries'));
    }
    */
    
    public function index(Request $request)
    {
        return view('admin.countries.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        
        $model = Country::select('*');
        
        
        $total = $model->count();


        if (is_array($search) && !empty($search['value'])) {
            $model->whereRaw("name like '%{$search['value']}%'");
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
        return view('admin.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryRequest $request)
    {
		$validated = $request->validated();
        
		Country::create($validated);
		
        return redirect()->route('admin.countries.index')
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
		$country = Country::find($id);
        
        return view('admin.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CountryRequest $request, $id)
    {        
		$validated = $request->validated();

        $country = Country::find($id);
        $country->update($validated);
		
        return redirect()->route('admin.countries.index')
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
        $country = Country::find($id);
        $country->delete();
        
        return redirect()->route('admin.countries.index')
			->with('status', __('The record has been deleted successfully'));
    }

    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $countries = Country::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`name`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`name`) AS text"), 'id')
            ->orderBy('name', 'asc')->limit(25);
            
        $results = $countries->get();
        
        return response()->json($results);
    }
}
