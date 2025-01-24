@extends('ticket::layouts.master')

@section('page', trans('ticket::admin.status-index-title'))

@section('ticket_header')
{!! link_to_route(
    $setting->grab('admin_route').'.status.create',
    trans('ticket::admin.btn-create-new-status'), null,
    ['class' => 'btn btn-primary'])
!!}
@stop

@section('ticket_content_parent_class', 'p-0')

@section('ticket_content')
    @if ($statuses->isEmpty())
        <h3 class="text-center">{{ trans('ticket::admin.status-index-no-statuses') }}
            {!! link_to_route($setting->grab('admin_route').'.status.create', trans('ticket::admin.status-index-create-new')) !!}
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
            @foreach($statuses as $status)
                <tr>
                    <td style="vertical-align: middle">
                        {{ $status->id }}
                    </td>
                    <td style="color: {{ $status->color }}; vertical-align: middle">
                        {{ $status->name }}
                    </td>
                    <td>
                        {!! link_to_route(
                                                $setting->grab('admin_route').'.status.edit', trans('ticket::admin.btn-edit'), $status->id,
                                                ['class' => 'btn btn-info'] )
                            !!}

                            {!! link_to_route(
                                                $setting->grab('admin_route').'.status.destroy', trans('ticket::admin.btn-delete'), $status->id,
                                                [
                                                'class' => 'btn btn-danger deleteit',
                                                'form' => "delete-$status->id",
                                                "node" => $status->name
                                                ])
                            !!}
                        {!! CollectiveForm::open([
                                        'method' => 'DELETE',
                                        'route' => [
                                                    $setting->grab('admin_route').'.status.destroy',
                                                    $status->id
                                                    ],
                                        'id' => "delete-$status->id"
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
            if (confirm("{!! trans('ticket::admin.status-index-js-delete') !!}" + $(this).attr("node") + " ?"))
            {
                $form = $(this).attr("form");
                $("#" + $form).submit();
            }

        });
    </script>
@append
