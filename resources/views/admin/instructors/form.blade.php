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

<form name="instructors" @isset($instructor)
		action="{{ route('admin.instructors.update', $instructor->id) }}"
	@else
		action="{{ route('admin.instructors.store') }}"
	@endisset method="POST" enctype="multipart/form-data">
	
	@isset($instructor)
		@method('PUT')
	@endisset
	@csrf

    <div class="row">
        <div class="form-group col-md-6">
            <label for="name">{{ __('Name') }}</label>
            <input type="text" name="name" id="name" @isset($instructor) value="{{ old('name') ?? $instructor->name }}" @else value="{{ old('name') }}" @endisset class="form-control" placeholder="{{ __('Name') }}" required>
        </div>
        
        <div class="form-group col-md-6">
            <label for="lastName">{{ __('Last Name') }}</label>
            <input type="text" name="last_name" id="lastName" @isset($instructor) value="{{ old('last_name') ?? $instructor->last_name }}" @else value="{{ old('last_name') }}" @endisset class="form-control" placeholder="{{ __('Last Name') }}" required>
        </div>
    </div>
    
    <div class="row">
        <div class="form-group col-md-6">
            <label for="email">{{ __('Email') }}</label>
            <input type="email" name="email" id="email" @isset($instructor) value="{{ old('email') ?? $instructor->email }}" @else value="{{ old('email') }}" @endisset class="form-control" placeholder="{{ __('Email') }}" required>
        </div>
        
        <div class="form-group col-md-6">
            <label for="phone">{{ __('Phone') }}</label>
            <input type="text" name="phone" id="phone" @isset($instructor) value="{{ old('phone') ?? $instructor->phone }}" @else value="{{ old('phone') }}" @endisset class="form-control" placeholder="{{ __('Phone') }}" required>
        </div>
    </div>
    
    <div class="row">
        <div class="form-group col-md-6">
            <label for="documentNumber">{{ __('Document Number') }}</label>
            <input type="text" name="document_number" id="documentNumber" @isset($instructor) value="{{ old('document_number') ?? $instructor->document_number }}" @else value="{{ old('document_number') }}" @endisset class="form-control" placeholder="{{ __('Document Number') }}" required>
        </div>
        
        <div class="form-group col-md-6">
            <label for="birthday">{{ __('Birthday') }}</label>
            <input type="text" name="birthday" id="birthday" @if(isset($instructor) && $instructor->birthday) value="{{ old('birthday') ?? $instructor->birthday->format('d-m-Y') }}" @else value="{{ old('birthday') }}" @endif class="form-control" placeholder="{{ __('Birthday') }}" required>
        </div>
    </div>
    
    <div class="row">
        <div class="form-group col-md-6">
            <label for="countryId">{{ __('Country') }}</label>
            @if (request()->user()->hasAnyRole(['latam-manager','admin']))
                <select class="form-control" name="country_id" id="countryId">
                    @isset($student->city)
                        <option value="{{ $student->city->country_id }}" selected="selected">{{ $student->city->country->name }}</option>
                    @endisset
                </select>
            @elseif (request()->user()->hasAnyRole(['instructor','country-manager']))
                <select class="form-control" name="country_id" id="countryId" disabled>
                    <option value="{{ request()->user()->city->country_id }}" selected>{{ request()->user()->city->country->name }}</option>
                </select>
            @endif
        </div>
    
        <div class="form-group col-md-6">
            <label for="cityId">{{ __('City') }}</label>
            <select class="form-control" name="city_id" id="cityId">
                @isset($instructor->city_id)
                    <option value="{{ $instructor->city_id }}" selected="selected">{{ $instructor->city->name }}</option>
                @endisset
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="form-group col-md-6">
            <label for="academyId">{{ __('Academy') }}</label>
            <select class="form-control" name="academy_id" id="academyId">
                @isset($instructor->academy_id)
                    <option value="{{ $instructor->academy_id }}" selected="selected">{{ $instructor->academy->name }}</option>
                @endisset
            </select>
        </div>    
    
        <div class="form-group col-md-6">
            <label for="gradeId">{{ __('Grade') }}</label>
            <select class="form-control" name="grade_id" id="gradeId" required>
                <option value="">null</option>        
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" @if((isset($instructor) && $instructor->grade_id == $grade->id) || $grade->id == old('grade_id')) selected @endif>{{ $grade->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
		<label for="profilePhoto">{{ __('Profile Photo') }}</label>
        <div class="file-loading">
            @php
                $preview = [];
            
                if (!empty($instructor->profile_photo)) {
                    $url = $instructor->profile_photo;
                    
                    $preview = [
                        'initialPreview' => url('storage/'. $url),
                        'initialPreviewAsData' => true,
                        'initialPreviewConfig' => [
                            [
                                'caption' => $instructor->name,
                                'filename' => $instructor->name,
                                'downloadUrl' => url('storage/'. $url),
                                'size' => Storage::disk('public')->size($url),
                            ],
                        ],
                        'deleteUrl' => route('admin.users.profile-photo.delete', $instructor->id),
                        'deleteExtraData' => ['_token' => csrf_token()],
                        'ajaxDeleteSettings' => ['type' => 'DELETE'],
                        'overwriteInitial' => true,  
                    ];
                }            
            @endphp
            <input name="profile_photo" id="profilePhoto" type="file" data-preview='@json($preview)'>
        </div>
	</div>    
    
    <div class="form-group">
        <label></label>
        <div class="checkbox c-checkbox"><label>
            <input type="checkbox" name="active" value="1" @if((isset($instructor) && $instructor->active) || old('active')) checked="checked" @endif><span class="fa fa-check"></span> {{ __('Active') }}</label>
        </div>        
    </div>     
    
    @isset($instructor)
        <div class="form-group">
            <label></label>
            <div class="checkbox c-checkbox"><label>
                <input type="checkbox" name="change_password" value="1" @if(old('change_password')) checked="checked" @endif><span class="fa fa-check"></span> {{ __('Change Password') }}</label>
            </div>        
        </div>    
    @endisset
    
    <div class="row change-password">
        <div class="form-group col-md-6">
            <label for="password">{{ __('Password') }}</label>
            <input type="password" class="form-control" name="password" id="password" @isset($instructor) value="" @else required @endisset class="form-control" placeholder="{{ __('Password') }}">
        </div>

        <div class="form-group col-md-6">
            <label for="passwordConfirmation">{{ __('Confirm Password') }}</label>
            <input type="password" name="password_confirmation" id="passwordConfirmation" @isset($instructor) value="" @else required @endisset class="form-control" placeholder="{{ __('Confirm Password') }}">
        </div>
    </div>        
    
	<div class="form-group row mt-4">
		<div class="col-6">
			<button class="btn btn-sm btn-primary" type="submit">{{ __('Accept') }}</button>
			<a href="{{ route('admin.instructors.index') }}" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
		</div>
	</div>
</form>

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
        
        $('[name="birthday"]').datetimepicker({
            locale: 'es',
            format: 'DD-MM-YYYY',       
        });
        
        var $countryId = $('[name="country_id"]');
        var $cityId = $('[name="city_id"]');
        var $academyId = $('[name="academy_id"]');        
        
        $countryId.on('change', function(e, first) {
            var val = $(this).val();
            $cityId.prop('disabled', true);
            $academyId.prop('disabled', true);
            if (val) {
                $cityId.prop('disabled', false);
                $academyId.prop('disabled', false);
            }
            
            if (!first) {
                $cityId.val(null).trigger('change');
                $academyId.val(null).trigger('change');
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
        
        $academyId.select2({
            theme: "bootstrap4",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/academies/filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, country_id: $countryId.val() };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('Academy') }}",
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 0,
            templateResult: function(data) {        
                if (data.loading) { return data.text; }
                var markup = data.text;
                return markup;
            },
            templateSelection: function(data) { return data.text; }
        });        

        $('[name="grade_id"]').select2({
            placeholder: "{{ __('Grade') }}",
            allowClear: true,
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('[name="role_id"]').select2({
            placeholder: "{{ __('Role') }}",
            allowClear: true,
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('[name="role_id[]"]').select2({
            theme: "bootstrap4",
            ajax: {
                url: "{{ route('admin.roles.filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('Role') }}",
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 1,
            templateResult: function(data) {        
                if (data.loading) { return data.text; }
                var markup = data.text;
                return markup;
            },
            templateSelection: function(data) { return data.text; }
        });        

        var option = {
            language: "es",
            theme: "fas",
            allowedFileExtensions: ["jpg", "jpeg", "png", "gif"],
            browseClass: "btn btn-primary",
            showCaption: false,
            showRemove: true,
            showUpload: false,
            showCancel: false,
            showClose: false,
            maxImageHeight: 150,
            maxFileCount: 1,
            resizeImage: true            
        };
        
        var preview = $("[name='profile_photo']").data('preview');
        
        jQuery.extend(option, preview);
    
        $("[name='profile_photo']").fileinput(option);
        
        
        @isset($instructor)
            $('[name="change_password"]').change(function() {
                var checked = $(this).prop('checked');
                if (checked === false) {
                    $('.row.change-password').addClass('d-none');
                } else {
                    $('.row.change-password').removeClass('d-none');
                }
            }).change();
        @endisset      
    });
</script>
@endpush