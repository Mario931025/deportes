@extends('admin.layouts.app')

@section('title', __('Assistances'))

@section('content')
<!-- Page content-->
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Assistances') }}</div>  
        <!-- Breadcrumb right aligned-->
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="#">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Assistances') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
                  
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Assistances List') }}</h3>
					<div class="row">
                        <div class="col">  
                            <form method="GET" id="filter" action="{{ route('admin.assistances.index') }}" accept-charset="UTF-8" role="search">
                                @if (request()->user()->hasAnyRole(['instructor','country-manager','latam-manager','admin']))
                                    <div class="form-row align-items-center">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="search" placeholder="{{ __('Search') }}" value="{{ request('search') }}" autofocus>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            @if (request()->user()->hasAnyRole(['latam-manager','admin']))
                                                <select class="form-control" name="country_id" id="countryId">
                                                    @if(request()->has('country_id'))
                                                        @php($country = App\Models\Country::find(request('country_id')))
                                                        @if(!is_null($country)) 
                                                            <option value="{{ $country->id }}" selected>{{ $country->name }}</option>
                                                        @endif
                                                    @endif
                                                </select>
                                            @elseif (request()->user()->hasRole('country-manager'))
                                                <select class="form-control" name="country_id" id="countryId" disabled>
                                                    <option value="{{ request()->user()->city->country_id }}" selected>{{ request()->user()->city->country->name }}</option>
                                                </select>
                                            @else
                                                <input type="text" class="form-control" value="{{ request()->user()->city->country->name }}" readonly>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            @if (request()->user()->hasAnyRole(['country-manager','latam-manager','admin']))
                                                <select class="form-control" name="academy_id" id="academyId">
                                                    @if(request()->has('country_id') && request()->has('academy_id'))      
                                                        @php($academy = App\Models\Academy::where(['country_id' => request('country_id'), 'id' => request('academy_id')])->first())
                                                        @if(!is_null($academy)) 
                                                            <option value="{{ $academy->id }}" selected>{{ $academy->name }}</option>
                                                        @endif
                                                    @endif
                                                </select>
                                            @else
                                                <input type="text" class="form-control" value="{{ request()->user()->academy->name }}" readonly>
                                            @endif                                                
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="form-row  align-items-center">
                                    <div class="col-md-3">
                                        <input type="text" name="date_from" id="dateFrom" value="{{ request('date_from') }}" class="form-control" placeholder="{{ __('Date From') }}">
                                    </div>    

                                    <div class="col-md-3">
                                        <input type="text" name="date_to" id="dateTo" value="{{ request('date_to') }}" class="form-control" placeholder="{{ __('Date To') }}">
                                    </div>        

                                    <div class="col-md-3">
                                        <div class="form-check mb-2 mb-md-0">
                                            <input name="exams" class="form-check-input" type="checkbox" id="exams" @if(request()->boolean('exams')) checked @endif>
                                            <label class="form-check-label" for="exams">
                                                {{ __('Exams') }}
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <button class="btn btn-primary btn-block" type="submit">{{ __('Filter') }}</button>
                                    </div>
                                </div>
                            </form>
                            
                            <hr>
                        </div>
					</div>
                    <!--{{--
					<div class="row">
                        <div class="col">
                            <a href="{{ route('admin.assistances.report', ['pdf'] + Request::all()) }}" class="card-link btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i> PDF</a>
                        </div>
					</div>
                    --}}-->
				</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100 datatables">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Academy') }}</th>
                                    <th>{{ __('Student') }}</th>
                                    <!--{{--<th style="width:10%;">{{ __('Action') }}</th>--}}-->
                                </tr>
                            </thead>
                            <tbody>
                                <!--{{--
                                @foreach ($assistances as $assistance)
                                    <tr>
                                        <td>{{ $assistance->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $assistance->created_at->format('H:i') }}</td>
                                        <td>{{ $assistance->academy->name }}</td>
                                        <td>@if($assistance->studentUser) {{ $assistance->studentUser->name }} {{ $assistance->studentUser->last_name }} @endif</td>
                                        !--<td class="text-center"></td>--
                                    </tr>
                                @endforeach
                                --}}-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--{{--
                <div class="card-footer">
                    <div class="pagination-wrapper"> {!! $assistances->appends(Request::all())->links() !!} </div>
                </div>
                --}}-->
            </div>
		</div>
	</div>
</div>
@endsection

@include('admin.table')

@push('vendor-styles')
<link rel="stylesheet" href="{{ asset('angle/vendor/select2/dist/css/select2.css') }}">
<link rel="stylesheet" href="{{ asset('angle/vendor/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css') }}">
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
<script src="{{ asset('angle/vendor/moment/min/moment-with-locales.js') }}"></script>
<script src="{{ asset('angle/vendor/bootstrap-datetimepicker/dist/js/bootstrap-datetimepicker.js') }}"></script>
@endpush

@push('styles')
<style>
.form-control, .select2-container {
    margin-top: .5rem;
    margin-bottom: .5rem;
}
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        
        var $countryId = $('[name="country_id"]');
        var $academyId = $('[name="academy_id"]');
        
        $countryId.on('change', function(e, first) {
            var val = $(this).val();
            $academyId.prop('disabled', true);
            if (val) {
                $academyId.prop('disabled', false);
            }
            
            if (!first) {
                $academyId.val(null).trigger('change');
            }            
        }).trigger('change', true);           
        
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
        
        $('[name="academy_id"]').select2({
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

        var dtIcons = {
            time: "fa fa-clock",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down",
            previous: 'fa fa-arrow-left',
            next: 'fa fa-arrow-right',          
        };
        
        var $dateFrom = $('[name="date_from"]'),
            $dateTo = $('[name="date_to"]');

        $dateFrom.datetimepicker({
            locale: 'es',
            format: 'DD-MM-YYYY',
            icons: dtIcons       
        });

        $dateTo.datetimepicker({
            locale: 'es',
            format: 'DD-MM-YYYY',
            icons: dtIcons          
        });

        $dateFrom.on('dp.change', function(e) {
            if ($(this).val() != '') {
                $dateTo.data("DateTimePicker").minDate(moment($(this).val(), 'DD-MM-YYYY'))
            }
        }).trigger('dp.change');
        
        $dateTo.on('dp.change', function(e) {
            if ($(this).val() != '') {
                $dateFrom.data("DateTimePicker").maxDate(moment($(this).val(), 'DD-MM-YYYY'))
            }            
        }).trigger('dp.change');


        var $dataTables, $filter;
        
        $dataTables = $('.datatables');
        $filter = $('#filter');
        
        $dataTables.on('list', function(e, param) {
            $(this).DataTable().destroy();
            $dataTables.find('tbody').empty();
            $(this).DataTable({
                
                dom: 'Bfrtip',
                buttons: [
                    {
                        text: '<i class="fa fa-download" aria-hidden="true"></i> PDF',
                        className: 'card-link btn btn-primary',
                        action: function(e, dt, node, config) {
                            var data = '';
                            
                            if (dt.context[0].json.report) {
                                data = '?_=' + btoa(JSON.stringify(dt.context[0].json.report));
                            }
                                                        
                            window.open("{{ url('assistances/report/pdf') }}"+ data, '_blank');
                        }
                    }
                ],                
                
                "serverSide": true,
                
                "ajax" : {
                    url: "{{ route('admin.assistances.get') }}",
                    type: "POST",
                    data: param,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                },

                "columns": [
                    { data: "created_at", name: 'created_at', type: 'date', orderable: true, className: 'text-left',
                        render: function(data, type, row, meta) {
                            return moment(new Date(data)).format('DD-MM-YYYY HH:mm');
                        },
                    },
                    { data: "_academy", name: '_academy', type: 'date', orderable: true, className: 'text-left' },
                    { data: "student", name: 'student', type: 'date', orderable: true, className: 'text-left' },
                ],
                
                "order": [[ 0, "desc" ]]
                               
            });
        });
        
        $filter.on('submit', function(e) {
            e.preventDefault();
            var data = {};
            data.value = $filter.find('[name="search"]').val();
            data.country_id = $filter.find('[name="country_id"]').val();
            data.academy_id = $filter.find('[name="academy_id"]').val();
            data.date_from = $filter.find('[name="date_from"]').val();
            data.date_to = $filter.find('[name="date_to"]').val();
            data.exams = $filter.find('[name="exams"]').prop('checked');
            $dataTables.trigger('list', { search: data });
        });                
        
        $dataTables.trigger('list');        
    });
</script>
@endpush