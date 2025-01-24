@extends($master)
@section('page', trans('ticket::admin.priority-edit-title', ['name' => ucwords($priority->name)]))

@section('content')
    @include('ticket::shared.header')
    <div class="well bs-component">
        {!! CollectiveForm::model($priority, [
                                    'route' => [$setting->grab('admin_route').'.priority.update', $priority->id],
                                    'method' => 'PATCH',
                                    'class' => 'form-horizontal'
                                    ]) !!}
        <legend>{{ trans('ticket::admin.priority-edit-title', ['name' => ucwords($priority->name)]) }}</legend>
        @include('ticket::admin.priority.form', ['update', true])
        {!! CollectiveForm::close() !!}
    </div>
@stop
