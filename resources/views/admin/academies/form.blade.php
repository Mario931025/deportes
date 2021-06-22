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

<form name="academies" @isset($academy)
		action="{{ route('admin.academies.update', $academy->id) }}"
	@else
		action="{{ route('admin.academies.store') }}"
	@endisset method="POST" enctype="multipart/form-data">
	
	@isset($academy)
		@method('PUT')
	@endisset
	@csrf

    <div class="form-group">
        <label for="name">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" @isset($academy) value="{{ old('name') ?? $academy->name }}" @else value="{{ old('name') }}" @endisset class="form-control" placeholder="{{ __('Name') }}" required>
    </div>
    
    @if(auth()->user()->hasAnyRole(['latam-manager','admin']))
        <div class="form-group">
            <label for="countryId">{{ __('Country') }}</label>
            <select class="form-control" name="country_id" id="countryId">
                @isset($academy->country_id)
                    <option value="{{ $academy->country_id }}" selected="selected">{{ $academy->country->name }}</option>
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
    
    <div class="form-group">
        <label for="cityId">{{ __('City') }}</label>
        <select class="form-control" name="city_id" id="cityId">
            @isset($academy->city_id)
                <option value="{{ $academy->city_id }}" selected="selected">{{ $academy->city->name }}</option>
            @endisset
        </select>
    </div>    
    
    <div class="form-group">
        <label></label>
        <div class="checkbox c-checkbox"><label>
            <input type="checkbox" name="active" value="1" @if((isset($academy) && $academy->active) || old('active')) checked="checked" @endif><span class="fa fa-check"></span> {{ __('Active') }}</label>
        </div>        
    </div>       
    
	<div class="form-group row mt-4">
		<div class="col-6">
			<button class="btn btn-sm btn-primary" type="submit">{{ __('Accept') }}</button>
			<a href="{{ route('admin.academies.index') }}" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
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
        
        var $countryId = $('[name="country_id"]');
        var $cityId = $('[name="city_id"]');   

        $countryId.on('change', function(e, first) {
            var val = $(this).val();
            $cityId.prop('disabled', true);
            if (val) {
                $cityId.prop('disabled', false);
            }
            
            if (!first) {
                $cityId.val(null).trigger('change');
            }             
        }).trigger('change', true);        
        
        $cityId.select2({
            theme: "bootstrap4",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/cities/filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, country_id: $countryId.val() };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('City') }}",
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 0,
            templateResult: function(data) {        
                if (data.loading) { return data.text; }
                var markup = data.text;
                return markup;
            },
            templateSelection: function(data) { return data.text; }
        });
        
        $countryId.select2({
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