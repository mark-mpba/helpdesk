@extends($master)
@section('page', trans('ticket::admin.category-edit-title', ['name' => ucwords($category->name)]))

@section('content')
    @include('ticket::shared.header')
    <div class="well bs-component">
        {!! CollectiveForm::model($category, [
                                    'route' => [$setting->grab('admin_route').'.category.update', $category->id],
                                    'method' => 'PATCH',
                                    'class' => 'form-horizontal'
                                    ]) !!}
        <legend>{{ trans('ticket::admin.category-edit-title', ['name' => ucwords($category->name)]) }}</legend>
        @include('ticket::admin.category.form', ['update', true])
        {!! CollectiveForm::close() !!}
    </div>
@stop
