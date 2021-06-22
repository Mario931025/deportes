@extends('admin.layouts.app')

@section('title', __('Edit City'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Cities') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.cities.index') }}">{{ __('Cities') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit City') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Edit City') }}</h3>
				</div>			
                <div class="card-body">
					@include('admin.cities.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection