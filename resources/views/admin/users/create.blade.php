@extends('admin.layouts.app')

@section('title', __('Create User'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Users') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('Users') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create User') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Create User') }}</h3>
				</div>			
                <div class="card-body">
					@include('admin.users.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection