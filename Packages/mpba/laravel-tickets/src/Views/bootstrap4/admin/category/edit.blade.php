@extends('ticket::layouts.master')
@section('page', trans('ticket::admin.category-edit-title', ['name' => ucwords($category->name)]))

@section('ticket_content')
    {!! CollectiveForm::model($category, [
                                'route' => [$setting->grab('admin_route').'.category.update', $category->id],
                                'method' => 'PATCH',
                                'class' => ''
                                ]) !!}
        @include('ticket::admin.category.form', ['update', true])
    {!! CollectiveForm::close() !!}
@stop
