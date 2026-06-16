@extends('layouts.adminlte')

@section('title', 'Dashboard')

@section('content')

    @can('manager-access')
        @include('manager.partials.stat-cards')
        <div class="row mt-4">
            <div class="col-lg-6">
                @include('admin.partials.bookings-chart')
            </div>
            <div class="col-lg-6">
                @include('admin.partials.occupancy-trends')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-6">
                @include('admin.partials.top-reviews')
            </div>
        </div>
    @endcan
    @can('admin-access')
        @include('admin.partials.stat-cards')
        @include('admin.partials.booking-stats')
        <div class="row mt-4">
            <div class="col-lg-8">
                @include('admin.partials.bookings-chart')
            </div>
            <div class="col-lg-4">
                @include('admin.partials.bookings-city-list')
            </div>

        </div>

        <div class="row mt-4">
            <div class="col-lg-6" style="color: #e65151">
                @include('admin.partials.occupancy-trends')

                @include('admin.partials.financial-charts')
            </div>
            <div class="col-lg-6">
                @include('admin.partials.top-reviews')
            </div>

        </div>
    @endcan

@endsection
