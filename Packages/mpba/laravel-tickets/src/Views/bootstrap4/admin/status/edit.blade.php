@extends('ticket::layouts.master')
@section('page', trans('ticket::admin.status-edit-title', ['name' => ucwords($status->name)]))

@section('ticket_content')
    {!! CollectiveForm::model($status, [
                                    'route' => [$setting->grab('admin_route').'.status.update', $status->id],
                                    'method' => 'PATCH'
                                    ]) !!}
        @include('ticket::admin.status.form', ['update', true])
    {!! CollectiveForm::close() !!}
@stop
