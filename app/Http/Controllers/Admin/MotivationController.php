<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motivation;
use App\Http\Requests\Admin\MotivationRequest;

class MotivationController extends Controller
{
    protected $types = ['absence', 'birthday'];
    
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
        
        $model = Motivation::select('*');
        
        if (!empty($search)) {
            $model->where('phrase', 'like', "%{$search}%");
        }
        
        $motivations = $model->paginate($paginate);
        
        return view('admin.motivations.index', compact('motivations'));
    }
    */
    
    public function index(Request $request)
    {
        return view('admin.motivations.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        
        $model = Motivation::select('*');
        
        
        $total = $model->count();


        if (is_array($search) && !empty($search['value'])) {
            $model->where(function($query) use ($search) {
                $words = explode(' ', $search['value']);

                foreach ($words as $word) {
                    $query->orWhereRaw("concat(' ',phrase) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ', (case type when 'birthday' then '". __('birthday') ."' when 'absence' then '". __('absence') ."' end)) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ', (case active when 1 then '". __('Yes') ."' when 0 then '". __('No') ."' end)) like '% ". $word ."%'");
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
            $item->_type = ucfirst(__($item->type));
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
        $types = $this->types;
        
        return view('admin.motivations.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MotivationRequest $request)
    {
		$validated = $request->validated();
        
        if (!$request->filled('active')) {
            $validated['active'] = false;
        }        
        
		Motivation::create($validated);
		
        return redirect()->route('admin.motivations.index')
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
        $types = $this->types;
		$motivation = Motivation::find($id);
        
        return view('admin.motivations.edit', compact('motivation', 'types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MotivationRequest $request, $id)
    {        
		$validated = $request->validated();
        
        if (!$request->filled('active')) {
            $validated['active'] = false;
        }        

        $motivation = Motivation::find($id);
        $motivation->update($validated);
		
        return redirect()->route('admin.motivations.index')
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
        $motivation = Motivation::find($id);
        $motivation->delete();
        
        return redirect()->route('admin.motivations.index')
			->with('status', __('The record has been deleted successfully'));
    }

    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $motivations = Motivation::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`phrase`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`phrase`) AS text"), 'id')
            ->orderBy('phrase', 'asc')->limit(25);
            
        $results = $motivations->get();
        
        return response()->json($results);
    }
}
