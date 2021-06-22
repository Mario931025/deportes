@extends('admin.layouts.app')

@section('title', __('Edit Instructor'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Instructors') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.instructors.index') }}">{{ __('Instructors') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit Instructor') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Edit Instructor') }}</h3>
				</div>			
                <div class="card-body">
					@include('admin.instructors.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection