@extends('admin.layouts.app')

@section('title', __('Profile'))

@section('content')

<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Profile') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Profile') }}</li>
        </ol>        
	</div>
    <div class="row">
    
        <div class="col-xl-12">
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
        </div>
        
        <div class="col-lg-3">
            <div class="card b">
                <div class="card-header bg-gray-lighter text-bold">{{ __('Personal Settings') }}</div>
                <div class="list-group">
                    <a class="list-group-item list-group-item-action active" href="#personal-information" data-toggle="tab">{{ __('Personal Information') }}</a>
                    <a class="list-group-item list-group-item-action" href="#social-networks" data-toggle="tab">{{ __('Social Networks') }}</a>
                    <a class="list-group-item list-group-item-action" href="#change-password" data-toggle="tab">{{ __('Change Password') }}</a>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="tab-content p-0 b0">
                <div class="tab-pane active" id="personal-information">
                    @include('admin.profile.personal-information')
                </div>
                <div class="tab-pane" id="social-networks">
                    @include('admin.profile.social-networks')
                </div>
                <div class="tab-pane" id="change-password">
                    @include('admin.profile.change-password')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendor-styles')
<link rel="stylesheet" href="{{ asset('angle/vendor/select2/dist/css/select2.css') }}">
<link rel="stylesheet" href="{{ asset('angle/vendor/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('angle/vendor/kartik-v-bootstrap-fileinput//css/fileinput.min.css') }}">
<link rel="stylesheet" href="{{ asset('angle/vendor/bootstrap-datetimepicker/dist/css/bootstrap-datetimepicker.min.css') }}">
<style>
.select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
    color: #b7bac9 !important;
}

body .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered,
body .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
    line-height: 33px !important;
    margin-left: 4px !important;
}
.select2-container .select2-search--inline .select2-search__field {
    padding: 0 0.7rem;
    width: 100% !important;
}
.select2-container .select2-search--inline .select2-search__field::placeholder {
    color: #b7bac9 !important;
}
.select2-container--bootstrap4 .select2-selection--multiple {
    line-height: 17px !important;
    height: 35px !important;
    min-height: 35px !important;
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
<script src="{{ asset('angle/vendor/moment/min/moment-with-locales.js') }}"></script>
<script src="{{ asset('angle/vendor/bootstrap-datetimepicker/dist/js/bootstrap-datetimepicker.js') }}"></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('a[href="' + window.location.hash + '"]').trigger('click');
    });
</script>
@endpush