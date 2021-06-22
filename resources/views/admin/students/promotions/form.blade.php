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

<form name="promotions" action="{{ route('admin.students.promotions.store', $student->id) }}" method="POST">
	@csrf

    <div class="form-group">
        <label for="gradeId">{{ __('Grade') }}</label>
        <select class="form-control" name="grade_id" id="gradeId" required>
            {{--@isset($academy->country_id)
                <option value="{{ $academy->country_id }}" selected="selected">{{ $academy->country->name }}</option>
            @endisset--}}
        </select>
    </div>

    @if(auth()->user()->hasAnyRole(['country-manager','latam-manager','admin']))
        <div class="form-group">
            <label for="instructorUserId">{{ __('Instructor') }}</label>
            <select class="form-control" name="instructor_user_id" id="instructorUserId" required></select>
        </div>    
    @else
        <div class="form-group">
            <label for="instructorUserId">{{ __('Instructor') }}</label>
            <select class="form-control" name="instructor_user_id" id="instructorUserId" disabled>
                <option value="{{ auth()->user()->id }}" selected="selected">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</option>
            </select>
        </div>    
    @endif
    
	<div class="form-group row mt-4">
		<div class="col-6">
			<button class="btn btn-sm btn-primary" type="submit">{{ __('Accept') }}</button>
			<a href="{{ route('admin.students.promotions', $student->id) }}" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
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
        $('[name="grade_id"]').select2({
            theme: "bootstrap4",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/grades/filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('Grade') }}",
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 0,
            templateResult: function(data) {        
                if (data.loading) { return data.text; }
                var markup = data.text;
                return markup;
            },
            templateSelection: function(data) { return data.text; }
        }); 

        $('[name="instructor_user_id"]').select2({
            theme: "bootstrap4",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/users/filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, role_id: 2  };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('Instructor') }}",
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