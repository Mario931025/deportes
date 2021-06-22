@extends('admin.layouts.app')

@section('title', __('Edit Motivation'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Motivations') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.motivations.index') }}">{{ __('Motivations') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit Motivation') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Edit Motivation') }}</h3>
				</div>			
                <div class="card-body">
					@include('admin.motivations.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection