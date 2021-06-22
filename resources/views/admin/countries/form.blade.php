@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())   
    @component('admin.alert')
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    @endcomponent
@endif

<form name="countries" @isset($country)
		action="{{ route('admin.countries.update', $country->id) }}"
	@else
		action="{{ route('admin.countries.store') }}"
	@endisset method="POST" enctype="multipart/form-data">
	
	@isset($country)
		@method('PUT')
	@endisset
	@csrf

    <div class="form-group">
        <label for="name">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" @isset($country) value="{{ old('name') ?? $country->name }}" @else value="{{ old('name') }}" @endisset class="form-control" placeholder="{{ __('Name') }}" required>
    </div>    
    
	<div class="form-group row mt-4">
		<div class="col-6">
			<button class="btn btn-sm btn-primary" type="submit">{{ __('Accept') }}</button>
			<a href="{{ route('admin.countries.index') }}" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
		</div>
	</div>
</form>