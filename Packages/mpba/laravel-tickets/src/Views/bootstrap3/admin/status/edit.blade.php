@extends($master)
@section('page', trans('ticket::admin.status-edit-title', ['name' => ucwords($status->name)]))

@section('content')
    @include('ticket::shared.header')
    <div class="well bs-component">
        {!! CollectiveForm::model($status, [
                                    'route' => [$setting->grab('admin_route').'.status.update', $status->id],
                                    'method' => 'PATCH',
                                    'class' => 'form-horizontal'
                                    ]) !!}
        <legend>{{ trans('ticket::admin.status-edit-title', ['name' => ucwords($status->name)]) }}</legend>
        @include('ticket::admin.status.form', ['update', true])
        {!! CollectiveForm::close() !!}
    </div>
@stop
