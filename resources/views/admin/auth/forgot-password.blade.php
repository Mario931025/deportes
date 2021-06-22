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

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.email') }}" class="mb-3" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <div class="input-group with-focus">
                        <input class="form-control border-right-0 @error('email') is-invalid @enderror" id="email" type="email" placeholder="{{ __('E-Mail Address') }}" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>                            
                        <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-envelope"></em></span></div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror   							
                    </div>                     
                </div>
               
                <button class="btn btn-block btn-primary mt-3" type="submit">{{ __('Send Password Reset Link') }}</button>
            </form>
        </div>
    </div><!-- END card-->
</div>
@endsection
