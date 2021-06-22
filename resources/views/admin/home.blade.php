@extends('admin.layouts.app')

@section('title', __('Main'))

@section('content')
<!-- Page content-->
<div class="content-wrapper">
	<div class="content-heading">
		<div>{{ __('Main') }}</div>     
	</div>
    <div class="row">
        @if (Auth::user()->hasAnyRole(['country-manager', 'latam-manager', 'admin']))
            <div class="col-xl-3 col-md-6">
                <!-- START card-->
                <div class="card flex-row align-items-center align-items-stretch border-0">
                    <div class="col-4 d-flex align-items-center bg-primary-dark justify-content-center rounded-left">
                        <em class="fa fa-school fa-3x"></em>
                    </div>
                    <div class="col-8 py-3 bg-primary rounded-right">
                        <div class="h2 mt-0">{{ $academies }}</div>
                        <div class="text-uppercase">{{ __('Academies') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <!-- START card-->
                <div class="card flex-row align-items-center align-items-stretch border-0">
                    <div class="col-4 d-flex align-items-center bg-green-dark justify-content-center rounded-left">
                        <em class="fa fa-user-graduate fa-3x"></em>
                    </div>
                    <div class="col-8 py-3 bg-green rounded-right">
                        <div class="h2 mt-0">{{ $students }}</div>
                        <div class="text-uppercase">{{ __('Students') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <!-- START card-->
                <div class="card flex-row align-items-center align-items-stretch border-0">
                    <div class="col-4 d-flex align-items-center bg-yellow-dark justify-content-center rounded-left">
                        <em class="fa fa-user-check fa-3x"></em>
                    </div>
                    <div class="col-8 py-3 bg-yellow rounded-right">
                        <div class="h2 mt-0">{{ $instructors }}</div>
                        <div class="text-uppercase">{{ __('Instructors') }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection


@push('styles')
<style>

</style>
@endpush

@push('scripts')

@endpush