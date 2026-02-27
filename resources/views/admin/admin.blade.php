@extends('admin.layouts.base')

@section('content')
    @include('admin.partials.stats')
    @include('admin.partials.chart-panel')
    @include('admin.partials.devices-table')
    @include('admin.partials.inference-table')
    @include('admin.partials.gallery-grid')
@endsection
