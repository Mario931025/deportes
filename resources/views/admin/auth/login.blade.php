@extends('admin.layouts.auth')

@section('title', __('Login'))

@section('content')
    <div class="block-center mt-4 wd-xl">
        <!-- START card-->
        <div class="card card-flat">
            <div class="card-header text-center bg-dark">
                <a href="#"><img class="block-center" src="{{ asset('angle/img/logo.png') }}" alt="Image"></a>
            </div>
            <div class="card-body">
                <p class="text-center py-2">{{ __('Login') }}</p>
                <form method="POST" action="{{ route('login') }}" class="mb-3" id="loginForm">
                    @csrf
                    
                    @if ($errors->any())   
                        <div class="alert alert-danger" role="alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    
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
                    <div class="form-group">
                        <div class="input-group with-focus">
                            <input class="form-control border-right-0 @error('password') is-invalid @enderror" id="password" type="password" placeholder="{{ __('Password') }}" name="password" required autocomplete="current-password" required>
                            <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-lock"></em></span></div>
							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror                          
						</div>                      
                    </div>
                    <div class="clearfix">
                        <div class="checkbox c-checkbox float-left mt-0">
                            <label><input type="checkbox" value="" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}><span class="fa fa-check"></span> {{ __('Remember Me') }}</label>
                        </div>
                        @if (Route::has('password.request'))
                            <div class="float-right"><a class="text-muted" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a></div>
                        @endif
                    </div>
                    @if(env('GOOGLE_RECAPTCHA_KEY'))
                        @error('g-recaptcha-response')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="form-group justify-content-center">                            
                             <div class="g-recaptcha"
                                  data-sitekey="{{env('GOOGLE_RECAPTCHA_KEY')}}">
                             </div>
                        </div>
                    @endif                     
                    <button class="btn btn-block btn-primary mt-3" type="submit">{{ __('Login') }}</button>
                </form>
                <p class="pt-3 text-center">{{ __('Or') }}</p>
                <a class="btn btn-block bg-primary-dark" href="{{ url('login/facebook') }}"><span class="fab fa-facebook"></span> {{ __('Facebook') }}</a>
                <a class="btn btn-block bg-danger-dark" href="{{ url('login/google') }}"><span class="fab fa-google"></span> {{ __('Google') }}</a>
            </div>
        </div><!-- END card-->
    </div>
@endsection

@push('styles')
<style>
.wd-xl {
    width: 335px !important;
}
</style>
@endpush

@push('vendor-scripts')
<!--<script src="{{ asset('angle/vendor/parsleyjs/dist/parsley.js') }}"></script>-->
<!--<script src='https://www.google.com/recaptcha/api.js'></script>-->
@endpush