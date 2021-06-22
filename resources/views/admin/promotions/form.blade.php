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

<form name="promotions" @isset($promotion)
		action="{{ route('admin.promotions.update', $promotion->id) }}"
	@else
		action="{{ route('admin.promotions.store') }}"
	@endisset method="POST" enctype="multipart/form-data">
	
	@isset($promotion)
		@method('PUT')
	@endisset
	@csrf

    <div class="form-group">
        <label for="gradeId">{{ __('Grade') }}</label>
        <select class="form-control" name="grade_id" id="gradeId" required>
            @isset($promotion->grade_id)
                <option value="{{ $promotion->grade_id }}" selected="selected">{{ $promotion->grade->name }}</option>
            @endisset
        </select>
    </div>

    <div class="form-group">
        <label for="studentUserId">{{ __('Student') }}</label>
        <select class="form-control" name="student_user_id" id="studentUserId" required>
            @isset($promotion->student_user_id)
                <option value="{{ $promotion->student_user_id }}" selected="selected">{{ $promotion->studentUser->name }} {{ $promotion->studentUser->last_name }}</option>
            @endisset
        </select>
    </div>

    @if(auth()->user()->hasAnyRole(['country-manager','latam-manager','admin']))
        <div class="form-group">
            <label for="instructorUserId">{{ __('Instructor') }}</label>
            <select class="form-control" name="instructor_user_id" id="instructorUserId" required>
                @isset($promotion->instructor_user_id)
                    <option value="{{ $promotion->instructor_user_id }}" selected="selected">{{ $promotion->instructorUser->name }} {{ $promotion->instructorUser->last_name }}</option>
                @endisset
            </select>
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
			<a href="{{ route('admin.promotions.index') }}" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
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

        $('[name="student_user_id"]').select2({
            theme: "bootstrap4",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/users/filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, role_id: 1  };
                },
                processResults: function (data, params) {
                    return { results: data };
                },
            },
            placeholder: "{{ __('Student') }}",
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