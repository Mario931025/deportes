<div class="card b">
    <div class="card-header bg-gray-lighter text-bold">{{ __('Two Factor Authentication') }}</div>
    <div class="card-body">
        <form action="{{ url('/user/two-factor-authentication') }}" method="POST">
            @csrf
            <button class="btn btn-primary" type="submit">{{ __('two-factor-authentication') }}</button>
        </form>    
    </div>
</div> 