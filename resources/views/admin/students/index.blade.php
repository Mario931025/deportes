@extends('admin.layouts.app')

@section('title', __('Students'))

@section('content')
<!-- Page content-->
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Students') }}</div>  
        <!-- Breadcrumb right aligned-->
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="#">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Students') }}</li>
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
					<h3>{{ __('Students List') }}</h3>
					<div class="row">
                        <div class="col">  
                            <form method="GET" id="filter" action="{{ route('admin.assistances.index') }}" accept-charset="UTF-8" role="search">
                                <div class="form-row align-items-center">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="search" placeholder="{{ __('Search') }}" value="{{ request('search') }}" autofocus>
                                    </div>
                                    
                                    <div class="col-md-6">
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
                                </div>
                                
                                <div class="form-row  align-items-center">
                                    <div class="col-md-4">
                                        @if (request()->user()->hasAnyRole(['country-manager','latam-manager','admin']))
                                            <select class="form-control" name="city_id" id="cityId">
                                                @if(request()->has('city_id'))
                                                    @php($city = App\Models\City::find(request('city_id')))
                                                    @if(!is_null($city)) 
                                                        <option value="{{ $city->id }}" selected>{{ $city->name }}</option>
                                                    @endif
                                                @endif
                                            </select>
                                        @else
                                            <input type="text" class="form-control" value="{{ request()->user()->city->name }}" readonly>
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

                                    <div class="col-md-4">
                                        <button class="btn btn-primary btn-block" type="submit">{{ __('Filter') }}</button>
                                    </div>
                                </div>
                            </form>
                            
                            <hr>
                        </div>
					</div>                      
					<div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.students.create') }}" class="btn btn-success">{{ __('Create') }}</a>
                        </div>
                        <div class="col-md-6">
                            <!--{{--
                            <form method="GET" id="filter" action="{{ route('admin.students.index') }}" accept-charset="UTF-8" class="form-inline float-right" role="search">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search') }}" value="{{ request('search') }}" autofocus>
                                    <span class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </form>
                            --}}-->
                        </div>
					</div>
				</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100 datatables">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Last Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('City') }}</th>
                                    <th>{{ __('Academy') }}</th>
                                    <th>{{ __('Active') }}</th>
                                    <th style="width:20%;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--{{--
                                @foreach ($students as $student)
                                    <tr>
                                        <td>{{ $student->id }}</td>
                                        <td class="text-nowrap">{{ $student->name }}</td>
                                        <td class="text-nowrap">{{ $student->last_name }}</td>
                                        <td class="text-nowrap">{{ $student->email }}</td>
                                        <td class="text-nowrap">{{ $student->phone }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.students.assistances', $student->id) }}" class="btn btn-sm btn-warning" data-toggle="tooltip" title="{{ __('Assistances') }}"><em class="fa fa-book"></em></a>
                                            <a href="{{ route('admin.students.promotions', $student->id) }}" class="btn btn-sm btn-success" data-toggle="tooltip" title="{{ __('Promotions') }}"><em class="fa fa-arrow-up"></em></a>                                        
                                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-info"><em class="icon-pencil" data-toggle="tooltip" title="{{ __('Edit') }}"></em></a>
                                            <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}" class="d-inline-block">
                                                @method('DELETE')
                                                @csrf
                                                <button class="btn btn-sm btn-danger" onclick="return confirm(&quot;¿Estás seguro?&quot;)" data-destroy="" type="submit" data-toggle="tooltip" title="{{ __('Delete') }}"><em class="icon-trash"></em></button>
                                            </form>                                            
                                        </td>
                                    </tr>
                                @endforeach
                                --}}-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--{{--
                <div class="card-footer">
                    <div class="pagination-wrapper"> {!! $students->appends(['search' => Request::get('search')])->links() !!} </div>
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
        var $cityId = $('[name="city_id"]');
        var $academyId = $('[name="academy_id"]');
        
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

        $cityId.on('change', function(e, first) {
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
        
        $academyId.select2({
            theme: "bootstrap4",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/academies/filter') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, country_id: $countryId.val(), city_id: $cityId.val() };
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
                "serverSide": true,
                
                "ajax" : {
                    url: "{{ route('admin.students.get') }}",
                    type: "POST",
                    data: param,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                },

                "columns": [
                    { data: "id", width: '5%', name: 'id', type: 'integer', orderable: true, className: 'text-left' },
                    { data: "name", name: 'name', type: 'string', orderable: true, className: 'text-left' },
                    { data: "last_name", name: 'last_name', type: 'string', orderable: true, className: 'text-left' },
                    { data: "email", name: 'email', type: 'string', orderable: true, className: 'text-left' },
                    { data: "phone", name: 'phone', type: 'string', orderable: true, className: 'text-left' },
                    { data: "city.country.name", name: 'country', type: 'string', orderable: true, className: 'text-left' },
                    { data: "city.name", name: 'city', type: 'string', orderable: true, className: 'text-left' },
                    { data: "academy.name", name: 'academy', type: 'string', orderable: true, className: 'text-left' },
                    { data: "_active", name: 'active', type: 'string', orderable: true, className: 'text-left' },
                    { data: null, width: '10%', render: 'id', orderable: false, className: 'text-center',
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).empty();
                                                        
                            $('<a>', { href: "{{ url('/') }}/students/" + rowData.id + "/edit" })
                                .prop('title', "{{ __('Edit') }}").addClass('btn btn-sm btn-info mr-1')
                                .html('<em class="icon-pencil"></em>').appendTo($(td)).tooltip('show');

                            if ({{ Auth::user()->id }} !== rowData.id) {
                                var $form = $('<form>', { method: 'POST', action: "{{ url('/') }}/students/" + rowData.id })
                                    .addClass('d-inline-block').appendTo($(td));
                                $('<input>', { type: 'hidden', name: '_method' }).val('DELETE').appendTo($form);
                                $('<input>', { type: 'hidden', name: '_token' }).val('{{ csrf_token() }}').appendTo($form);
                                $('<button>', { type: 'submit' }).attr('data-destroy', rowData.id)
                                    .prop('title', "{{ __('Delete') }}").addClass('btn btn-sm btn-danger')
                                    .html('<em class="icon-trash"></em>').appendTo($form).tooltip('show');
                            }                                    
                        }
                    },
                ],
                               
            });
        });
        
        $filter.on('submit', function(e) {
            e.preventDefault();
            var data = {};
            data.value = $filter.find('[name="search"]').val();
            data.value = $filter.find('[name="search"]').val();
            data.country_id = $filter.find('[name="country_id"]').val();
            data.city_id = $filter.find('[name="city_id"]').val();
            data.academy_id = $filter.find('[name="academy_id"]').val();            
            $dataTables.trigger('list', { search: data });
        });                
        
        $dataTables.trigger('list');
    }); 
</script>
@endpush