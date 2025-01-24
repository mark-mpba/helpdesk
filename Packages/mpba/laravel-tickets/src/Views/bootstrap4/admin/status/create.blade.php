@extends('ticket::layouts.master')
@section('page', trans('ticket::admin.status-create-title'))

@section('ticket_content')
    {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.status.store', 'method' => 'POST', 'class' => '']) !!}
        @include('ticket::admin.status.form')
    {!! CollectiveForm::close() !!}
@stop
