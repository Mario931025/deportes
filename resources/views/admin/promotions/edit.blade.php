@extends('admin.layouts.app')

@section('title', __('Edit Promotion'))

@section('content')
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Promotions') }}</div>
        <ol class="breadcrumb ml-auto">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('Main') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">{{ __('Promotions') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Edit Promotion') }}</li>
        </ol>        
	</div>
	<div class="row">
		<div class="col-xl-12">
            <div class="card card-default">
				<div class="card-header">
					<h3>{{ __('Edit Promotion') }}</h3>
				</div>			
                <div class="card-body">
					@include('admin.promotions.form')
                </div>
            </div>
		</div>
	</div>
</div>
@endsection