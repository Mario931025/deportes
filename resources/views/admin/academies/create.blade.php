@extends('admin.layouts.app')

@section('title', __('Create Academy'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Academies') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.academies.index') }}">{{ __('Academies') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create Academy') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Create Academy') }}</h3>
				</div>			
                <div class="card-body">
					@include('admin.academies.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection