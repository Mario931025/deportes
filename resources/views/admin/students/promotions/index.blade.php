@extends('admin.layouts.app')

@section('title', __('Promotions'))

@section('content')
<!-- Page content-->
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Promotions') }}</div>  
        <!-- Breadcrumb right aligned-->
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">{{ __('Students') }}</a></li>
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
                    <h4 class="text-muted">{{ $student->name }} {{ $student->last_name }}</h4>
					<div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.students.promotions.create', $student->id) }}" class="btn btn-success">{{ __('Create') }}</a>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.students.promotions', $student->id) }}" accept-charset="UTF-8" class="form-inline float-right" role="search">
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
                                    <th>{{ __('Grade') }}</th>
                                    <th style="width:10%;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($promotions as $promotion)
                                    <tr>
                                        <td class="text-nowrap">{{ $promotion->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td class="text-nowrap">{{ $promotion->instructorUser ? $promotion->instructorUser->name .' '. $promotion->instructorUser->last_name : '' }}</td>
                                        <td class="text-nowrap">{{ $promotion->grade->name }}</td>
                                        <td class="text-center">
                                            @if ($loop->first)
                                                <form method="POST" action="{{ route('admin.students.promotions.destroy', [$student->id, $promotion->id]) }}" class="d-inline-block">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button class="btn btn-sm btn-danger" onclick="return confirm(&quot;¿Estás seguro?&quot;)" data-destroy="" type="submit" data-toggle="tooltip" title="{{ __('Delete') }}"><em class="icon-trash"></em></button>
                                                </form>
                                            @endif                                                
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="pagination-wrapper"> {!! $promotions->appends(['search' => Request::get('search')])->render() !!} </div>
                </div>
            </div>
		</div>
	</div>
</div>
@endsection