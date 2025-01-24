@extends($master)
@section('page', trans('ticket::admin.status-create-title'))

@section('content')
    @include('ticket::shared.header')
    <div class="well bs-component">
        {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.status.store', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
            <legend>{{ trans('ticket::admin.status-create-title') }}</legend>
            @include('ticket::admin.status.form')
        {!! CollectiveForm::close() !!}
    </div>
@stop
