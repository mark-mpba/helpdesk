@extends('ticket::layouts.master')
@section('page', trans('ticket::projects.project.create-title'))

@section('ticket_content')
    {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.project.store', 'method' => 'POST', 'class' => '']) !!}
        @include('ticket::admin.projects.form')
    {!! CollectiveForm::close() !!}
@stop
