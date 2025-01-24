@extends('ticket::layouts.master')

@section('page', trans('ticket::admin.administrator-index-title'))

@section('ticket_header')
{!! link_to_route(
    $setting->grab('admin_route').'.administrator.create',
    trans('ticket::admin.btn-create-new-administrator'), null,
    ['class' => 'btn btn-primary'])
!!}
@stop

@section('ticket_content_parent_class', 'p-0')

@section('ticket_content')
    @if ($administrators->isEmpty())
        <h3 class="text-center">{{ trans('ticket::admin.administrator-index-no-administrators') }}
            {!! link_to_route($setting->grab('admin_route').'.administrator.create', trans('ticket::admin.administrator-index-create-new')) !!}
        </h3>
    @else
        <div id="message"></div>
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>{{ trans('ticket::admin.table-id') }}</th>
                <th>{{ trans('ticket::admin.table-name') }}</th>
                <th>{{ trans('ticket::admin.table-remove-administrator') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($administrators as $administrator)
                <tr>
                    <td>
                        {{ $administrator->id }}
                    </td>
                    <td>
                        {{ $administrator->name }}
                    </td>
                    <td>
                        {!! CollectiveForm::open([
                        'method' => 'DELETE',
                        'route' => [
                                    $setting->grab('admin_route').'.administrator.destroy',
                                    $administrator->id
                                    ],
                        'id' => "delete-$administrator->id"
                        ]) !!}
                        {!! CollectiveForm::submit(trans('ticket::admin.btn-remove'), ['class' => 'btn btn-danger']) !!}
                        {!! CollectiveForm::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @endif

@stop
