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

<form name="motivations" @isset($motivation)
		action="{{ route('admin.motivations.update', $motivation->id) }}"
	@else
		action="{{ route('admin.motivations.store') }}"
	@endisset method="POST">
	
	@isset($motivation)
		@method('PUT')
	@endisset
	@csrf

    <div class="form-group">
        <label for="phrase">{{ __('Phrase') }}</label>
        <input type="text" name="phrase" id="phrase" @isset($motivation) value="{{ old('phrase') ?? $motivation->phrase }}" @else value="{{ old('phrase') }}" @endisset class="form-control" placeholder="{{ __('Phrase') }}" required>
    </div>
    
    <div class="form-group">
        <label for="type">{{ __('Type') }}</label>
        <select name="type" id="type" class="form-control">
            <option value=""></option>
            @foreach($types as $type)
                <option value="{{ $type }}" @if((isset($motivation) && $motivation->type == $type) || $type == old('type')) selected @endif>{{ ucfirst(__($type)) }}</option>
            @endforeach
        </select>        
    </div>

    <div class="form-group">
        <label></label>
        <div class="checkbox c-checkbox"><label>
            <input type="checkbox" name="active" value="1" @if((isset($motivation) && $motivation->active) || old('active')) checked="checked" @endif><span class="fa fa-check"></span> {{ __('Active') }}</label>
        </div>        
    </div>
    
	<div class="form-group row mt-4">
		<div class="col-6">
			<button class="btn btn-sm btn-primary" type="submit">{{ __('Accept') }}</button>
			<a href="{{ route('admin.motivations.index') }}" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
		</div>
	</div>
</form>

@push('vendor-styles')
<link rel="stylesheet" href="{{ asset('angle/vendor/select2/dist/css/select2.css') }}">
<link rel="stylesheet" href="{{ asset('angle/vendor/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css') }}">
@endpush

@push('vendor-scripts')
<script src="{{ asset('angle/vendor/select2/dist/js/select2.full.js') }}"></script>
<script src="{{ asset('angle/vendor/select2/dist/js/i18n/es.js') }}"></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {        
        $('[name="type"]').select2({
            theme: "bootstrap4",
            allowClear: true,
            placeholder: "{{ __('Type') }}",
            width: '100%',
        });
    });
</script>
@endpush