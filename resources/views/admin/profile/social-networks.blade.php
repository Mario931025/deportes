<div class="card b">
    <div class="card-header bg-gray-lighter text-bold">{{ __('Social Networks') }}</div>
    <div class="card-body">
        <form action="{{ route('admin.profile.social-networks') }}" method="POST">
            @method('PUT')
            @csrf

            <div class="form-group">
                <label for="facebook">{{ __('Facebook') }}</label>            
                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text"><em class="fa icon-social-facebook"></em></span></div>
                    <input type="text" name="facebook" id="facebook" value="{{ old('facebook') ?? ($user->socialNetwork ? $user->socialNetwork->facebook : '') }}" class="form-control" placeholder="{{ __('Facebook') }}" aria-label="{{ __('Twitter') }}">
                </div>
            </div>
            
            <div class="form-group">
                <label for="instagram">{{ __('Instagram') }}</label>            
                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text"><em class="fa icon-social-instagram"></em></span></div>
                    <input type="text" name="instagram" id="instagram" value="{{ old('instagram') ?? ($user->socialNetwork ? $user->socialNetwork->instagram : '') }}" class="form-control" placeholder="{{ __('Instagram') }}" aria-label="{{ __('Twitter') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="twitter">{{ __('Twitter') }}</label>            
                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text"><em class="fa icon-social-twitter"></em></span></div>
                    <input type="text" name="twitter" id="twitter" value="{{ old('twitter') ?? ($user->socialNetwork ? $user->socialNetwork->twitter : '') }}" class="form-control" placeholder="{{ __('Twitter') }}" aria-label="{{ __('Twitter') }}">
                </div>
            </div>
            
            <div class="form-group">
                <label for="pinterest">{{ __('Pinterest') }}</label>            
                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text"><em class="fa icon-social-pinterest"></em></span></div>
                    <input type="text" name="pinterest" id="pinterest" value="{{ old('pinterest') ?? ($user->socialNetwork ? $user->socialNetwork->pinterest : '') }}" class="form-control" placeholder="{{ __('Pinterest') }}" aria-label="{{ __('Twitter') }}">
                </div>
            </div>
            
            <div class="form-group">
                <label for="youtube">{{ __('Youtube') }}</label>            
                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text"><em class="fa icon-social-youtube"></em></span></div>
                    <input type="text" name="youtube" id="youtube" value="{{ old('youtube') ?? ($user->socialNetwork ? $user->socialNetwork->youtube : '') }}" class="form-control" placeholder="{{ __('Youtube') }}" aria-label="{{ __('Twitter') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="linkedin">{{ __('LinkedIn') }}</label>            
                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text"><em class="fa icon-social-linkedin"></em></span></div>
                    <input type="text" name="linkedin" id="linkedin" value="{{ old('linkedin') ?? ($user->socialNetwork ? $user->socialNetwork->linkedin : '') }}" class="form-control" placeholder="{{ __('LinkedIn') }}" aria-label="{{ __('Twitter') }}">
                </div>
            </div>            

            <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
        </form>        
    </div>
</div> 