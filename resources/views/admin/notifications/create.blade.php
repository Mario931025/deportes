@extends('admin.layouts.app')

@section('title', __('Send Notifications'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Send Notifications') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Send Notifications') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Send Notifications') }}</h3>
				</div>			
                <div class="card-body">
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

                    <form name="notifications"  action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf
                                                
                        <div class="form-group">
                            <label>{{ __('Title') }}</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="{{ __('Title') }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('Message') }}</label>
                            <textarea class="form-control" name="message" placeholder="{{ __('Message') }}">{{ old('message') }}</textarea>
                        </div>
                        
                        @if (auth()->user()->hasAnyRole(['country-manager','latam-manager','admin']))
                            @if(auth()->user()->hasAnyRole(['latam-manager','admin']))
                                <div class="form-group">
                                    <label for="countryId">{{ __('Country') }}</label>
                                    <select class="form-control" name="country_id" id="countryId">
                                        @if (old('country_id'))
                                            <option value="{{ old('country_id') }}" selected="selected">{{ App\Models\Country::find(old('country_id'))->first()->name }}</option>
                                        @endif                            
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
                        @endif 
                        
                        <div class="form-group row mt-4">
                            <div class="col-6">
                                <button class="btn btn-sm btn-primary" type="submit">{{ __('Send') }}</button>
                                <a href="{{ route('admin.home') }}" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
		</div>
	</div>
</div>
@endsection

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
            minimumInputLength: 1,
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
