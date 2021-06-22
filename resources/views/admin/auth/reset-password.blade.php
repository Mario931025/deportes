@extends('admin.layouts.auth')

@section('title', __('Reset Password'))

@section('content')
<div class="block-center mt-4 wd-xl">
    <!-- START card-->
    <div class="card card-flat">
        <div class="card-header text-center bg-dark">
            <a href="#"><img class="block-center" src="{{ asset('angle/img/logo.png') }}" alt="Image"></a>
        </div>
        <div class="card-body">
            <p class="text-center py-2">{{ __('Reset Password') }}</p>             
            
            @if ($errors->updatePassword->any())   
                @component('admin.alert')
                    @foreach ($errors->updatePassword->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                @endcomponent
            @endif          
            
            <form method="POST" action="{{ route('password.update') }}" class="mb-3" id="loginForm">
                @csrf
                
                <input type="hidden" name="token" value="{{ $request->route('token') }}">                
                
                <div class="form-group">
                    <div class="input-group with-focus">
                        <input class="form-control border-right-0 @error('email') is-invalid @enderror" id="email" type="email" placeholder="{{ __('E-Mail Address') }}" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>                            
                        <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-envelope"></em></span></div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>                     
                </div>
            
                <div class="form-group">
                    <div class="input-group with-focus">
                        <input class="form-control border-right-0 @error('password') is-invalid @enderror" id="password" type="password" placeholder="{{ __('Password') }}" name="password" autocomplete="current-password" required autocomplete="new-password">
                        <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-lock"></em></span></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group with-focus">
                        <input class="form-control border-right-0" id="password-confirm" type="password" placeholder="{{ __('Confirm Password') }}" name="password_confirmation" required autocomplete="new-password">
                        <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-lock"></em></span></div>
                    </div>
                </div>                 
               
                <button class="btn btn-block btn-primary mt-3" type="submit">{{ __('Reset Password') }}</button>
            </form>
        </div>
    </div><!-- END card-->
</div>
@endsection
