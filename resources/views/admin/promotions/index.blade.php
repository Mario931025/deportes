@extends('admin.layouts.app')

@section('title', __('Promotions'))

@section('content')
<!-- Page content-->
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Promotions') }}</div>  
        <!-- Breadcrumb right aligned-->
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="#">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Promotions') }}</li>
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
					<h3>{{ __('Promotions List') }}</h3>
					<div class="row">
                        <div class="col-md-6">
                            @if (auth()->user()->hasAnyRole(['instructor', 'country-manager','latam-manager','admin']))
                                <a href="{{ route('admin.promotions.create') }}" class="btn btn-success">{{ __('Create') }}</a>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <form method="GET" id="filter" action="{{ route('admin.promotions.index') }}" accept-charset="UTF-8" class="form-inline float-right" role="search">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search') }}" value="{{ request('search') }}" autofocus>
                                    <span class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </form>
                        </div>
					</div>
				</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100 datatables">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Student') }}</th>
                                    <th>{{ __('Instructor') }}</th>
                                    <th>{{ __('Grade') }}</th>
                                    <th style="width:10%;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--{{--
                                @foreach ($promotions as $promotion)
                                    <tr>
                                        <td>{{ $promotion->id }}</td>
                                        <td>{{ $promotion->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $promotion->studentUser ? $promotion->studentUser->name .' '. $promotion->studentUser->last_name : '' }}</td>                                        
                                        <td>{{ $promotion->instructorUser ? $promotion->instructorUser->name .' '. $promotion->instructorUser->last_name : '' }}</td>
                                        <td>{{ $promotion->grade->name }}</td>
                                        <td class="text-center">
                                            @if (auth()->user()->hasAnyRole(['instructor', 'country-manager','latam-manager','admin']))
                                                <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-info"><em class="icon-pencil" data-toggle="tooltip" title="{{ __('Edit') }}"></em></a>
                                                <form method="POST" action="{{ route('admin.promotions.destroy', $promotion->id) }}" class="d-inline-block">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button class="btn btn-sm btn-danger" onclick="return confirm(&quot;¿Estás seguro?&quot;)" data-destroy="" type="submit" data-toggle="tooltip" title="{{ __('Delete') }}"><em class="icon-trash"></em></button>
                                                </form>                                            
                                            @endif
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
                    <div class="pagination-wrapper"> {!! $promotions->appends(['search' => Request::get('search')])->links() !!} </div>
                </div>
                --}}-->
            </div>
		</div>
	</div>
</div>
@endsection

@include('admin.table')

@push('scripts')
<script src="{{ asset('angle/vendor/moment/min/moment-with-locales.js') }}"></script>
<script>
    $(document).ready(function() {
        var $dataTables, $filter;
        
        $dataTables = $('.datatables');
        $filter = $('#filter');
        
        $dataTables.on('list', function(e, param) {
            $(this).DataTable().destroy();
            $dataTables.find('tbody').empty();
            $(this).DataTable({
                "serverSide": true,
                
                "ajax" : {
                    url: "{{ route('admin.promotions.get') }}",
                    type: "POST",
                    data: param,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                },

                "columns": [
                    { data: "id", width: '5%', name: 'id', type: 'integer', orderable: true, className: 'text-left' },
                    { data: "created_at", name: 'created_at', type: 'date', orderable: true, className: 'text-center',
                        render: function(data, type, row, meta) {
                            return moment(new Date(data)).format('DD-MM-YYYY HH:mm');
                        },
                    },
                    { data: "student", name: 'student', type: 'string', orderable: true, className: 'text-left' },
                    { data: "instructor", name: 'instructor', type: 'string', orderable: true, className: 'text-left' },
                    { data: "grade", name: 'grade', type: 'string', orderable: true, className: 'text-left' },
                    { data: null, width: '10%', render: 'id', orderable: false, className: 'text-center',
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).empty();
                            
                            @if (auth()->user()->hasAnyRole(['instructor', 'country-manager','latam-manager','admin']))
                                $('<a>', { href: "{{ url('/') }}/promotions/" + rowData.id + "/edit" })
                                    .prop('title', "{{ __('Edit') }}").addClass('btn btn-sm btn-info mr-1')
                                    .html('<em class="icon-pencil"></em>').appendTo($(td)).tooltip('show');

                                var $form = $('<form>', { method: 'POST', action: "{{ url('/') }}/promotions/" + rowData.id })
                                    .addClass('d-inline-block').appendTo($(td));
                                $('<input>', { type: 'hidden', name: '_method' }).val('DELETE').appendTo($form);
                                $('<input>', { type: 'hidden', name: '_token' }).val('{{ csrf_token() }}').appendTo($form);
                                $('<button>', { type: 'submit' }).attr('data-destroy', rowData.id)
                                    .prop('title', "{{ __('Delete') }}").addClass('btn btn-sm btn-danger')
                                    .html('<em class="icon-trash"></em>').appendTo($form).tooltip('show');                  
                            @endif
                        }
                    },
                ],
                               
            });
        });
        
        $filter.on('submit', function(e) {
            e.preventDefault();
            var data = {};
            data.value = $filter.find('[name="search"]').val();
            $dataTables.trigger('list', { search: data });
        });                
        
        $dataTables.trigger('list');
    }); 
</script>
@endpush