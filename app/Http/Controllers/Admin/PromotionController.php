<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Grade;
use App\Http\Requests\Admin\PromotionRequest;

class PromotionController extends Controller
{
    public function __construct()
    {
        //$this->middleware('role:instructor,country-manager,latam-manager,admin')->except(['index', 'filter']);
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
        
        $model = Promotion::select('*');
                        
        if ($request->user()->hasRole('instructor')) {
            $model->where(function($query) use ($request) {
                $query->where('student_user_id', $request->user()->id)
                      ->orWhere('instructor_user_id', $request->user()->id);
            });
        } elseif ($request->user()->hasRole('student')) {
            $model->where('student_user_id', $request->user()->id);
        }
        
        if (!empty($search)) {
            $model->where('name', 'like', "%{$search}%");
        }
        
        $promotions = $model->paginate($paginate);
                
        return view('admin.promotions.index', compact('promotions'));
    }
    */
    
    
    public function index(Request $request)
    {
        return view('admin.promotions.index');
    }    
    
    public function get(Request $request)
    {
        $draw = $request->get('draw') ?? 1;
        $search = $request->get('search');
        $start = $request->get('start') ?? 0;
        $length = $request->get('length') ?? 10;
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        
        $model = Promotion::select(['promotions.*', \DB::raw("concat(s.name,' ',s.last_name) as student"),
            \DB::raw("concat(i.name,' ',i.last_name) as instructor"), 'g.name as grade']);
            
        $model->leftJoin('users as s', 's.id', 'promotions.student_user_id')
            ->leftJoin('users as i', 'i.id', 'promotions.instructor_user_id')
            ->join('grades as g', 'g.id', 'promotions.grade_id');
                        
        if ($request->user()->hasRole('instructor')) {
            $model->where(function($query) use ($request) {
                $query->where('student_user_id', $request->user()->id)
                      ->orWhere('instructor_user_id', $request->user()->id);
            });
        } elseif ($request->user()->hasRole('student')) {
            $model->where('student_user_id', $request->user()->id);
        }
                
        $total = $model->count();


        if (is_array($search) && !empty($search['value'])) {
            $model->where(function($query) use ($search) {
                $words = explode(' ', $search['value']);
                
                foreach ($words as $word) {
                    $query->orWhereRaw("concat(' ',date_format(promotions.created_at, '%d %m %Y %H:%i')) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',i.name,' ',i.last_name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',s.name,' ',s.last_name) like '% ". $word ."%'")
                        ->orWhereRaw("concat(' ',g.name) like '% ". $word ."%'");
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
        $grades = Grade::all();
        
        return view('admin.promotions.create', compact('grades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PromotionRequest $request)
    {
		$validated = $request->validated();
                
		Promotion::create($validated);
        
        $user = \App\Models\User::find($validated['student_user_id']);
        $user->grade_id = $validated['grade_id'];
        $user->save();
		
        return redirect()->route('admin.promotions.index')
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
        $grades = Grade::all();        
		$promotion = Promotion::find($id);
        
        return view('admin.promotions.edit', compact('promotion', 'grades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PromotionRequest $request, $id)
    {        
		$validated = $request->validated();
        
        $promotion = Promotion::find($id);
        $promotion->update($validated);
        
        $user = \App\Models\User::find($validated['student_user_id']);
        $user->grade_id = $validated['grade_id'];
        $user->save();        
		
        return redirect()->route('admin.promotions.index')
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
        $promotion = Promotion::find($id);
        $promotion->delete();
        
        return redirect()->route('admin.promotions.index')
			->with('status', __('The record has been deleted successfully'));
    }

    public function filter(Request $request)
    {
        $words = explode(' ', $request->q);
        
        $promotions = Promotion::where(function($query) use ($words) {
                $query->where(\DB::raw("concat(' ',`name`)"), 'like', '% '. join('% ', $words) .'%');
            })
            ->select(\DB::raw("concat(`name`) AS text"), 'id')
            ->orderBy('name', 'asc')->limit(25);
            
        $results = $promotions->get();
        
        return response()->json($results);
    }
}
