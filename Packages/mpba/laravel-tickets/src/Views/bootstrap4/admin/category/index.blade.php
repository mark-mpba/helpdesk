@extends('ticket::layouts.master')

@section('page', trans('ticket::admin.category-index-title'))

@section('ticket_header')
{!! link_to_route(
    $setting->grab('admin_route').'.category.create',
    trans('ticket::admin.btn-create-new-category'), null,
    ['class' => 'btn btn-primary'])
!!}
@stop

@section('ticket_content_parent_class', 'p-0')

@section('ticket_content')
    @if ($categories->isEmpty())
        <h3 class="text-center">{{ trans('ticket::admin.category-index-no-categories') }}
            {!! link_to_route($setting->grab('admin_route').'.category.create', trans('ticket::admin.category-index-create-new')) !!}
        </h3>
    @else
        <div id="message"></div>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ trans('ticket::admin.table-id') }}</th>
                    <th>{{ trans('ticket::admin.table-name') }}</th>
                    <th>{{ trans('ticket::admin.table-action') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td style="vertical-align: middle">
                        {{ $category->id }}
                    </td>
                    <td style="color: {{ $category->color }}; vertical-align: middle">
                        {{ $category->name }}
                    </td>
                    <td>
                        {!! link_to_route(
                                                $setting->grab('admin_route').'.category.edit', trans('ticket::admin.btn-edit'), $category->id,
                                                ['class' => 'btn btn-info'] )
                            !!}

                            {!! link_to_route(
                                                $setting->grab('admin_route').'.category.destroy', trans('ticket::admin.btn-delete'), $category->id,
                                                [
                                                'class' => 'btn btn-danger deleteit',
                                                'form' => "delete-$category->id",
                                                "node" => $category->name
                                                ])
                            !!}
                        {!! CollectiveForm::open([
                                        'method' => 'DELETE',
                                        'route' => [
                                                    $setting->grab('admin_route').'.category.destroy',
                                                    $category->id
                                                    ],
                                        'id' => "delete-$category->id"
                                        ])
                        !!}
                        {!! CollectiveForm::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

@stop
@section('footer')
    <script>
        $( ".deleteit" ).click(function( event ) {
            event.preventDefault();
            if (confirm("{!! trans('ticket::admin.category-index-js-delete') !!}" + $(this).attr("node") + " ?"))
            {
                var form = $(this).attr("form");
                $("#" + form).submit();
            }

        });
    </script>
@append
