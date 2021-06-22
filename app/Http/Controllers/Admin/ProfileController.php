<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SocialNetwork;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Rules\Password;
use App\Actions\Fortify\PasswordValidationRules;

class ProfileController extends Controller
{
    use PasswordValidationRules;
    
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        return view('admin.profile.index', compact('user'));
    }

    public function updatePersonalInformation(ProfileRequest $request)
    {
        $redirect = redirect()->route('admin.profile.index', ['#personal-information']);
        
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string'],
            'document_number' => ['required', 'string'],
            'birthday' => ['required'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
        ]);
        
        if ($validator->fails()) {
            return $redirect->withErrors($validator)->withInput();
        }
        
        $validated = $validator->validated();

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->profile_photo->store('users/profile_photo', 'public');
        } else {
            unset($validated['profile_photo']);
        }
        
        $user = User::find(Auth::id());
        $user->update($validated);
        
        return $redirect->with('status', __('The personal information has been updated successfully'));
    }
    
    public function updateSocialNetworks(ProfileRequest $request)
    {
        $redirect = redirect()->route('admin.profile.index', ['#social-networks']);
        
        $validator = Validator::make($request->all(), [
            'facebook' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string'],
            'twitter' => ['nullable', 'string'],
            'pinterest' => ['nullable', 'string'],
            'youtube' => ['nullable', 'string'],
            'linkedin' => ['nullable', 'string'],
        ]);
        
        if ($validator->fails()) {
            return $redirect->withErrors($validator)->withInput();
        }
        
        $validated = $validator->validated();

        SocialNetwork::updateOrCreate(['user_id' => Auth::id()], $validated);
        
        return $redirect->with('status', __('The social networks has been updated successfully'));
    }

    public function changePassword(ProfileRequest $request)
    {
        $redirect = redirect()->route('admin.profile.index', ['#change-password']);
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => $this->passwordRules(),
        ]);
        
        $validator->after(function($validator) use ($request) {
            if (! Hash::check($request->current_password, $request->user()->password)) {
                $validator->errors()->add('current_password', __('Your current password is incorrect.'));
            }
        });        
        
        if ($validator->fails()) {
            return $redirect->withErrors($validator)->withInput();
        }        
        
        $validated = $validator->validated();
        
        $user = User::find(Auth::id());
        $user->update(['password' => Hash::make($validated['new_password'])]);
        
        return $redirect->with('status', __('The password has been updated successfully'));
    }
}
