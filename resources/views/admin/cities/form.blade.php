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

<form name="cities" @isset($city)
		action="{{ route('admin.cities.update', $city->id) }}"
	@else
		action="{{ route('admin.cities.store') }}"
	@endisset method="POST" enctype="multipart/form-data">
	
	@isset($city)
		@method('PUT')
	@endisset
	@csrf

    <div class="form-group">
        <label for="name">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" @isset($city) value="{{ old('name') ?? $city->name }}" @else value="{{ old('name') }}" @endisset class="form-control" placeholder="{{ __('Name') }}" required>
    </div>
    
    @if(auth()->user()->hasAnyRole(['latam-manager','admin']))
        <div class="form-group">
            <label for="countryId">{{ __('Country') }}</label>
            <select class="form-control" name="country_id" id="countryId" required>
                @isset($city->country_id)
                    <option value="{{ $city->country_id }}" selected="selected">{{ $city->country->name }}</option>
                @endisset
            </select>
        </div>
    @else
        <div class="form-group">
            <label for="countryId">{{ __('Country') }}</label>
            <select class="form-control" name="country_id" id="countryId" disabled>
                <option value="{{ auth()->user()->city->country_id }}" selected="selected">{{ auth()->user()->city->country->name }}</option>
            </select>
        </div>  
    @endif
    
	<div class="form-group row mt-4">
		<div class="col-6">
			<button class="btn btn-sm btn-primary" type="submit">{{ __('Accept') }}</button>
			<a href="{{ route('admin.cities.index') }}" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
		</div>
	</div>
</form>

@push('vendor-styles')
<link rel="stylesheet" href="{{ asset('angle/vendor/select2/dist/css/select2.css') }}">
<link rel="stylesheet" href="{{ asset('angle/vendor/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css') }}">
<style>
.select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
    color: #b7bac9 !important;
}

body .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered,
body .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
    line-height: 33px !important;
    margin-left: 4px !important;
}
</style>
@endpush

@push('vendor-scripts')
<script src="{{ asset('angle/vendor/select2/dist/js/select2.full.js') }}"></script>
<script src="{{ asset('angle/vendor/select2/dist/js/i18n/es.js') }}"></script>
<script src="{{ asset('angle/vendor/kartik-v-bootstrap-fileinput/js/plugins/piexif.min.js') }}"></script>
<script src="{{ asset('angle/vendor/kartik-v-bootstrap-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ asset('angle/vendor/kartik-v-bootstrap-fileinput/js/locales/es.js') }}"></script>
<script src="{{ asset('angle/vendor/kartik-v-bootstrap-fileinput/themes/fas/theme.js') }}"></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('[name="country_id"]').select2({
            theme: "bootstrap4",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/countries/filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('Country') }}",
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 0,
            templateResult: function(data) {        
                if (data.loading) { return data.text; }
                var markup = data.text;
                return markup;
            },
            templateSelection: function(data) { return data.text; }
        });   
    });
</script>
@endpush