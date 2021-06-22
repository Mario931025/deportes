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
            @if (! $request->recovery)
                <p class="text-center py-2">{{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}</p>
            @else
                <p class="text-center py-2">{{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}</p>
            @endif
            
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ url('/two-factor-challenge') }}" class="mb-3" id="loginForm">
                @csrf
                
                @if (! $request->recovery)
                    <div class="form-group">
                        <div class="input-group with-focus">
                            <input class="form-control border-right-0 @error('email') is-invalid @enderror" id="code" type="text" placeholder="{{ __('Code') }}" name="code" value="{{ old('code') }}" required autocomplete="code" autofocus>                            
                            <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-qrcode"></em></span></div>
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="float-right"><a class="text-muted" href="{{ url('/two-factor-challenge?recovery=true') }}">{{ __('Use a recovery code') }}</a></div>
                    </div>                    
                @else
                    <div class="form-group">
                        <div class="input-group with-focus">
                            <input class="form-control border-right-0 @error('recovery_code') is-invalid @enderror" id="recoveryCode" type="text" placeholder="{{ __('Recovery Code') }}" name="recovery_code" value="{{ old('recovery_code') }}" required autocomplete="recovery_code" autofocus>                            
                            <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-lock"></em></span></div>
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="float-right"><a class="text-muted" href="{{ url('/two-factor-challenge') }}">{{ __('Use an authentication code') }}</a></div>
                    </div>                    
                @endif
                
                <button class="btn btn-block btn-primary mt-3" type="submit">{{ __('Login') }}</button>
            </form>
        </div>
    </div><!-- END card-->
</div>
@endsection
