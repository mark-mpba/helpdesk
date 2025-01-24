@extends('ticket::layouts.master')
@section('page', trans('ticket::admin.priority-create-title'))

@section('ticket_content')
    {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.priority.store', 'method' => 'POST', 'class' => '']) !!}
        @include('ticket::admin.priority.form')
    {!! CollectiveForm::close() !!}
@stop
