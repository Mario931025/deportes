@extends('admin.layouts.app')

@section('title', __('Create Promotion'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Promotions') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">{{ __('Students') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.students.promotions', $student->id) }}">{{ __('Promotions') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create Promotion') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Create Promotion') }}</h3>
                    <h4 class="text-muted">{{ $student->name }} {{ $student->last_name }}</h4>
				</div>			
                <div class="card-body">
					@include('admin.students.promotions.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection