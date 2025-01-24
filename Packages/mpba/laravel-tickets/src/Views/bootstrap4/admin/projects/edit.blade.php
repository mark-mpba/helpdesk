@extends('ticket::layouts.master')

@section('page', trans('ticket::projects.project.edit-title', ['title' => ucwords($project->title)]))


@section('ticket_content')
    {!! CollectiveForm::model($project, [
        'route' => [$setting->grab('admin_route').'.project.update', $project->id],
        'method' => 'PATCH'
        ]) !!}
    @include('ticket::admin.projects.form', ['update', true])
    {!! CollectiveForm::close() !!}
@stop
