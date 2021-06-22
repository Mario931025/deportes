@extends('admin.layouts.app')

@section('title', __('Users'))

@section('content')
<!-- Page content-->
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Users') }}</div>  
        <!-- Breadcrumb right aligned-->
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="#">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Users') }}</li>
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
					<h3>{{ __('Users List') }}</h3>
					<div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success">{{ __('Create') }}</a>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" id="filter" action="{{ route('admin.users.index') }}" accept-charset="UTF-8" class="form-inline float-right" role="search">
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
                                    <th>{{ __('Last Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('City') }}</th>
                                    <th>{{ __('Academy') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Active') }}</th>                                      
                                    <th style="width:10%;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--{{--
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td class="text-nowrap">{{ $user->name }}</td>
                                        <td class="text-nowrap">{{ $user->last_name }}</td>
                                        <td class="text-nowrap">{{ $user->email }}</td>
                                        <td class="text-nowrap">{{ $user->phone }}</td>
                                        <td class="text-nowrap">{!! join("<br>", $user->roles->pluck('description')->toArray()) !!}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-info"><em class="icon-pencil" data-toggle="tooltip" title="{{ __('Edit') }}"></em></a>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline-block">
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
                    <div class="pagination-wrapper"> {!! $users->appends(['search' => Request::get('search')])->links() !!} </div>
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
                    url: "{{ route('admin.users.get') }}",
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
                    { data: "roles", name: 'roles', type: 'string', orderable: true, className: 'text-left' },
                    { data: "_active", name: 'active', type: 'string', orderable: true, className: 'text-left' },                           
                    { data: null, width: '10%', render: 'id', orderable: false, className: 'text-center',
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).empty();
                                                        
                            $('<a>', { href: "{{ url('/') }}/users/" + rowData.id + "/edit" })
                                .prop('title', "{{ __('Edit') }}").addClass('btn btn-sm btn-info mr-1')
                                .html('<em class="icon-pencil"></em>').appendTo($(td)).tooltip('show');

                            if ({{ Auth::user()->id }} !== rowData.id) {
                                var $form = $('<form>', { method: 'POST', action: "{{ url('/') }}/users/" + rowData.id })
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
            $dataTables.trigger('list', { search: data });
        });                
        
        $dataTables.trigger('list');
    }); 
</script>
@endpush