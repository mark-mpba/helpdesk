@extends($master)

@section('page')
    {{ trans('ticket::admin.priority-index-title') }}
@stop

@section('content')
    @include('ticket::shared.header')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>{{ trans('ticket::admin.priority-index-title') }}
                {!! link_to_route(
                                    $setting->grab('admin_route').'.priority.create',
                                    trans('ticket::admin.btn-create-new-priority'), null,
                                    ['class' => 'btn btn-primary pull-right'])
                !!}
            </h2>
        </div>

        @if ($priorities->isEmpty())
            <h3 class="text-center">{{ trans('ticket::admin.priority-index-no-priorities') }}
                {!! link_to_route($setting->grab('admin_route').'.priority.create', trans('ticket::admin.priority-index-create-new')) !!}
            </h3>
        @else
            <div id="message"></div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <td>{{ trans('ticket::admin.table-id') }}</td>
                        <td>{{ trans('ticket::admin.table-name') }}</td>
                        <td>{{ trans('ticket::admin.table-action') }}</td>
                    </tr>
                </thead>
                <tbody>
                @foreach($priorities as $priority)
                    <tr>
                        <td style="vertical-align: middle">
                            {{ $priority->id }}
                        </td>
                        <td style="color: {{ $priority->color }}; vertical-align: middle">
                            {{ $priority->name }}
                        </td>
                        <td>
                            {!! link_to_route(
                                                    $setting->grab('admin_route').'.priority.edit', trans('ticket::admin.btn-edit'), $priority->id,
                                                    ['class' => 'btn btn-info'] )
                                !!}

                                {!! link_to_route(
                                                    $setting->grab('admin_route').'.priority.destroy', trans('ticket::admin.btn-delete'), $priority->id,
                                                    [
                                                    'class' => 'btn btn-danger deleteit',
                                                    'form' => "delete-$priority->id",
                                                    "node" => $priority->name
                                                    ])
                                !!}
                            {!! CollectiveForm::open([
                                            'method' => 'DELETE',
                                            'route' => [
                                                        $setting->grab('admin_route').'.priority.destroy',
                                                        $priority->id
                                                        ],
                                            'id' => "delete-$priority->id"
                                            ])
                            !!}
                            {!! CollectiveForm::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@stop
@section('footer')
    <script>
        $( ".deleteit" ).click(function( event ) {
            event.preventDefault();
            if (confirm("{!! trans('ticket::admin.priority-index-js-delete') !!}" + $(this).attr("node") + " ?"))
            {
                $form = $(this).attr("form");
                $("#" + $form).submit();
            }

        });
    </script>
@append
