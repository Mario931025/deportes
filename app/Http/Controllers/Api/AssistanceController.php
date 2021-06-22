<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Assistance;
use App\Models\User;

class AssistanceController extends Controller
{
    public function code(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
                
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }
        
        $validated = $validator->validated();
        
        $code = Str::random(10);

        $data = array_merge([
            'code' => $code,
            'created_at' => new Carbon
        ], $validated);
        
        $result = $this->getAssistanceCodesTable()->updateOrInsert(['user_id' => $request->user()->id], $data);
        
        return response()->json(['code' => $code]);
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_exam' => 'nullable',
        ]);
        
        $assistanceCodeQuery = $this->getAssistanceCodesTable()
            ->where('code', $request->code)
            ->where('created_at', '>=', now()->subMinute());

        $validator->after(function($validator)
                use ($request, $assistanceCodeQuery) {
                
            if (! $assistanceCodeQuery->exists()) {
                $validator->errors()->add('code', __('The code is invalid or expired'));
            }
        });
                
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }
        
        $validated = $validator->validated();
        
        $assistanceCode = $assistanceCodeQuery->first();
        
        if (Assistance::where('student_user_id', $assistanceCode->user_id)
                ->whereDate('created_at', now())->exists()) {
            return response()->json(['message' => __('The student already registers attendance on the day')], 500);
        }        
                
        $assistance = Assistance::create([
            'student_user_id' => $assistanceCode->user_id,
            'student_longitude' => $assistanceCode->longitude,
            'student_latitude' => $assistanceCode->latitude,
            'instructor_user_id' => $request->user()->id,
            'instructor_longitude' => $request->longitude,
            'instructor_latitude' => $request->latitude,
            'academy_id' => User::find($assistanceCode->user_id)->academy_id,
            'is_exam' => $request->boolean('is_exam'),
        ]);
        
        $result = Assistance::with(['studentUser', 'instructorUser'])->find($assistance->id);
        $result->studentUser->profile_photo = $result->studentUser->profile_photo ? url('storage/'. $result->studentUser->profile_photo) : null;
        $result->instructorUser->profile_photo = $result->instructorUser->profile_photo ? url('storage/'. $result->instructorUser->profile_photo) : null;
        
        $assistanceCodeQuery->delete();

        return response()->json($result);
    }
 
    public function records(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pagination' => 'nullable|integer',
            'search' => 'nullable|string',
            'only_exam' => 'nullable',
        ]);
        
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }
        
        $validated = $validator->validated();

        $pagination = $validated['pagination'] ?? 10;
        
        $assistance = Assistance::with(['studentUser', 'instructorUser', 'academy'])->select(['*']);

        if (isset($validated['search'])) {
            DB::statement(DB::raw("SET lc_time_names = 'es_PY';"));
                        
            $assistance->where(function ($query) use ($validated) {
                $query->where(DB::raw('TIME_FORMAT(created_at, "%h:%i:%s")'), 'like', '%' . $validated['search'] . '%')
                      ->orWhereDate('created_at', 'like', '%' . $validated['search'] . '%')
                      ->orWhere(DB::raw('MONTHNAME(created_at)'), 'like', '%' . $validated['search'] . '%');
            });

            $assistance->orWhereHas('instructorUser', function($query) use ($validated) {
                $query->where('name', 'like', '%' . $validated['search'] . '%')
                      ->orWhere('last_name', 'like', '%' . $validated['search'] . '%');
            });
        }
        
        if ($request->boolean('only_exam')) {
            $assistance->where('is_exam', true);
        }
        
        $assistance->where(function ($query) use ($request) {
            if ($request->user()->roles->contains('id', 1)) {
                $query->orWhere('student_user_id', $request->user()->id);
            }
            
            if ($request->user()->roles->contains('id', 2)) {
                $query->orWhere('instructor_user_id', $request->user()->id);
            }
        });            
        
        $result = $assistance->paginate($pagination);
        
        $collection = $result->getCollection();
        $collection->map(function ($item, $key) {
            
            if (!filter_var($item->studentUser->profile_photo, FILTER_VALIDATE_URL) && $item->studentUser->profile_photo) {
                $item->studentUser->profile_photo = url('storage/'. $item->studentUser->profile_photo);
            }

            if (!filter_var($item->instructorUser->profile_photo, FILTER_VALIDATE_URL) && $item->instructorUser->profile_photo) {
                $item->instructorUser->profile_photo = url('storage/'. $item->instructorUser->profile_photo);
            }            
            
            //$item->studentUser->profile_photo = $item->studentUser->profile_photo;
            //$item->instructorUser->profile_photo = $item->instructorUser->profile_photo;
            
            return $item;
        });  

        logger($result);

        return response()->json($result, 200);
    }    
    
    protected function getAssistanceCodesTable()
    {
        return DB::table('assistance_codes');
    }    
}
