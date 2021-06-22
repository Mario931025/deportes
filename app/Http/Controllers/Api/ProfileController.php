<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = User::with('grade', 'roles', 'academy', 'academy.country',
                'city', 'city.country', 'socialNetwork')
            ->where('id', $request->user()->id)->first();
        
        $user->profile_photo = $user->profile_photo ? url('storage/'. $user->profile_photo) : null;
        
        return response()->json($user);
    }
    
    public function updateInformation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'document_number' => 'required|string',
            'birthday' => 'required',
            'city_id' => 'required|integer|exists:cities,id',
            'academy_id' => 'required|integer|exists:academies,id',
        ]);
                
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }
        
        $validated = $validator->validated();
        
        $result = $request->user()->update($validated);
        
        return response()->json(['message' => __('Profile information was updated correctly')]);
    }

    public function updateSocialNetworks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            'twitter' => 'nullable',
            'pinterest' => 'nullable',
            'youtube' => 'nullable',
            'linkedin' => 'nullable',
        ]);
                
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }
        
        $validated = $validator->validated();

        $request->user()->socialNetwork()->updateOrCreate(['user_id' => $request->user()->id], $validated);
        
        return response()->json(['message' => __('Social networks were updated correctly')]);
    }

    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);
                
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }
        
        $validated = $validator->validated();
        
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->profile_photo->store('users/profile_photo', 'public');
        }    
        
        $request->user()->update($validated);
        
        return response()->json(['message' => __('Profile photo was updated successfully')]);
    }

    public function deletePhoto(Request $request)
    {   
        $request->user()->update(['profile_photo' => null]);
        
        return response()->json(['message' => __('Profile photo was successfully deleted')]);
    }     
}
