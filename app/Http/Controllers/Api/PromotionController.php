<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function records(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pagination' => 'nullable|integer',
        ]);
        
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }
        
        $validated = $validator->validated();

        $pagination = $validated['pagination'] ?? 10;
        
        $promotion = Promotion::with(['studentUser', 'instructorUser', 'grade'])->select(['*']);
        
        $promotion->where('student_user_id', $request->user()->id);
        
        $result = $promotion->paginate($pagination);

        return response()->json($result, 200);
    }
}
