@extends('admin.layouts.app')

@section('title', __('Edit Country'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Countries') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.countries.index') }}">{{ __('Countries') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit Country') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Edit Country') }}</h3>
				</div>			
                <div class="card-body">
					@include('admin.countries.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection