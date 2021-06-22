@extends('admin.layouts.app')

@section('title', __('Assistances'))

@section('content')
<!-- Page content-->
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Assistances') }}</div>  
        <!-- Breadcrumb right aligned-->
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">{{ __('Students') }}</a></li>
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
                    <h4 class="text-muted">{{ $student->name }} {{ $student->last_name }}</h4>
					<div class="row">
                        <div class="col-md-6">
                            
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.students.assistances', $student->id) }}" accept-charset="UTF-8" class="form-inline float-right" role="search">
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
                        <table class="table w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Instructor') }}</th>
                                    <th>{{ __('Academy') }}</th>
                                    <th>{{ __('Exam') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assistances as $assistance)
                                    <tr>
                                        <td class="text-nowrap">{{ $assistance->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td class="text-nowrap">{{ $assistance->instructorUser->name }} {{ $assistance->instructorUser->last_name }}</td>
                                        <td class="text-nowrap">{{ $assistance->academy->name }}</td>
                                        <td class="text-nowrap">{{ $assistance->is_exam ? __('Yes') : __('No') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="pagination-wrapper"> {!! $assistances->appends(['search' => Request::get('search')])->render() !!} </div>
                </div>
            </div>
		</div>
	</div>
</div>
@endsection