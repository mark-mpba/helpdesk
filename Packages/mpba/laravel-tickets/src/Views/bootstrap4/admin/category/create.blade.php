@extends('ticket::layouts.master')
@section('page', trans('ticket::admin.category-create-title'))

@section('ticket_content')
    {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.category.store', 'method' => 'POST', 'class' => '']) !!}
        @include('ticket::admin.category.form')
    {!! CollectiveForm::close() !!}
@stop
