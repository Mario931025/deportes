@extends('admin.layouts.app')

@section('title', __('Create Student'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Students') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">{{ __('Students') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create Student') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Create Student') }}</h3>
				</div>			
                <div class="card-body">
					@include('admin.students.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection