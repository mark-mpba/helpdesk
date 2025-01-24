@extends($master)
@section('page', trans('ticket::admin.priority-create-title'))

@section('content')
    @include('ticket::shared.header')
    <div class="well bs-component">
        {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.priority.store', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
            <legend>{{ trans('ticket::admin.priority-create-title') }}</legend>
            @include('ticket::admin.priority.form')
        {!! CollectiveForm::close() !!}
    </div>
@stop
