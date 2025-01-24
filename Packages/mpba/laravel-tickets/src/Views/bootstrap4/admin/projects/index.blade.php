@extends('ticket::layouts.master')

@section('page', trans('ticket::projects.index-title'))

@section('ticket_header')
{!! link_to_route(
    $setting->grab('admin_route').'.project.create',
    trans('ticket::projects.btn.new'), null,
    ['class' => 'btn btn-primary'])
!!}
@stop

@section('ticket_content_parent_class', 'p-0')

@section('ticket_content')
    @if ($projects->isEmpty())
        <h3 class="text-center">{{ trans('ticket::projects.index-no-projects') }}
            {!! link_to_route($setting->grab('admin_route').'.project.create', trans('ticket::projects.index-create-new')) !!}
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
            @foreach($projects as $project)
                <tr>
                    <td style="vertical-align: middle">
                        {{ $project->id }}
                    </td>
                    <td style="color: {{ $project->color }}; vertical-align: middle">
                        {{ $project->title }}
                    </td>
                    <td>
                        {!! link_to_route(
                            $setting->grab('admin_route').'.project.edit', trans('ticket::projects.btn.edit'), $project->id,
                            ['class' => 'btn btn-info'] )
                        !!}

                        {!! link_to_route(
                            $setting->grab('admin_route').'.project.destroy', trans('ticket::projects.btn.delete'), $project->id,
                            [
                            'class' => 'btn btn-danger deleteit',
                            'form' => "delete-$project->id",
                            "node" => $project->title
                            ])
                        !!}
                        {!! CollectiveForm::open([
                            'method' => 'DELETE',
                            'route' => [
                                        $setting->grab('admin_route').'.project.destroy',
                                        $project->id
                                        ],
                            'id' => "delete-$project->id"
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
            if (confirm("{!! trans('ticket::projects.index-js-delete') !!}" + $(this).attr("node") + " ?"))
            {
                $form = $(this).attr("form");
                $("#" + $form).submit();
            }

        });
    </script>
@append
