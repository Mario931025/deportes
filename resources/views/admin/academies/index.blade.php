@extends('admin.layouts.app')

@section('title', __('Academies'))

@section('content')
<!-- Page content-->
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Academies') }}</div>  
        <!-- Breadcrumb right aligned-->
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="#">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Academies') }}</li>
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
					<h3>{{ __('Academies List') }}</h3>
					<div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.academies.create') }}" class="btn btn-success">{{ __('Create') }}</a>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" id="filter" action="{{ route('admin.academies.index') }}" accept-charset="UTF-8" class="form-inline float-right" role="search">
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('Active') }}</th>
                                    <th style="width:10%;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--{{--
                                @foreach ($academies as $academy)
                                    <tr>
                                        <td>{{ $academy->id }}</td>
                                        <td class="text-nowrap">{{ $academy->name }}</td>
                                        <td class="text-nowrap">{{ $academy->country->name }}</td>
                                        <td class="text-nowrap">{{ $academy->active ? __('Yes') : __('No') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.academies.edit', $academy->id) }}" class="btn btn-sm btn-info" data-toggle="tooltip" title="{{ __('Edit') }}"><em class="icon-pencil"></em></a>
                                            <form method="POST" action="{{ route('admin.academies.destroy', $academy->id) }}" class="d-inline-block">
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
                    <div class="pagination-wrapper"> {!! $academies->appends(['search' => Request::get('search')])->render() !!} </div>
                </div>
                --}}-->
            </div>
		</div>
	</div>
</div>
@endsection

@include('admin.table')

@push('scripts')
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
                    url: "{{ route('admin.academies.get') }}",
                    type: "POST",
                    data: param,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                },

                "columns": [
                    { data: "id", width: '5%', name: 'id', type: 'integer', orderable: true, className: 'text-left' },
                    { data: "name", name: 'name', type: 'string', orderable: true, className: 'text-left' },
                    { data: "country", name: 'country', type: 'string', orderable: true, className: 'text-left' },
                    { data: "_active", name: 'active', type: 'string', orderable: true, className: 'text-left' },
                    { data: null, width: '10%', render: 'id', orderable: false, className: 'text-center',
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).empty();
                                                        
                            $('<a>', { href: "{{ url('/') }}/academies/" + rowData.id + "/edit" })
                                .prop('title', "{{ __('Edit') }}").addClass('btn btn-sm btn-info mr-1')
                                .html('<em class="icon-pencil"></em>').appendTo($(td)).tooltip('show');

                            var $form = $('<form>', { method: 'POST', action: "{{ url('/') }}/academies/" + rowData.id })
                                .addClass('d-inline-block').appendTo($(td));
                            $('<input>', { type: 'hidden', name: '_method' }).val('DELETE').appendTo($form);
                            $('<input>', { type: 'hidden', name: '_token' }).val('{{ csrf_token() }}').appendTo($form);
                            $('<button>', { type: 'submit' }).attr('data-destroy', rowData.id)
                                .prop('title', "{{ __('Delete') }}").addClass('btn btn-sm btn-danger')
                                .html('<em class="icon-trash"></em>').appendTo($form).tooltip('show');                  
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