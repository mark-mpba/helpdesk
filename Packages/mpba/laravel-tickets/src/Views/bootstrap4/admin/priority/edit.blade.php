@extends('ticket::layouts.master')
@section('page', trans('ticket::admin.priority-edit-title', ['name' => ucwords($priority->name)]))

@section('ticket_content')
    {!! CollectiveForm::model($priority, [
                                'route' => [$setting->grab('admin_route').'.priority.update', $priority->id],
                                'method' => 'PATCH'
                                ]) !!}
        @include('ticket::admin.priority.form', ['update', true])
    {!! CollectiveForm::close() !!}
@stop
