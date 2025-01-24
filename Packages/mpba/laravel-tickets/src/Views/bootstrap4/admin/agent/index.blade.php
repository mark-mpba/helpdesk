@extends('ticket::layouts.master')

@section('page', trans('ticket::admin.agent-index-title'))

@section('ticket_header')
{!! link_to_route(
    $setting->grab('admin_route').'.agent.create',
    trans('ticket::admin.btn-create-new-agent'), null,
    ['class' => 'btn btn-primary'])
!!}
@stop

@section('ticket_content_parent_class', 'p-0')

@section('ticket_content')
    @if ($agents->isEmpty())
        <h3 class="text-center">{{ trans('ticket::admin.agent-index-no-agents') }}
            {!! link_to_route($setting->grab('admin_route').'.agent.create', trans('ticket::admin.agent-index-create-new')) !!}
        </h3>
    @else
        <div id="message"></div>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ trans('ticket::admin.table-id') }}</th>
                    <th>{{ trans('ticket::admin.table-name') }}</th>
                    <th>{{ trans('ticket::admin.table-categories') }}</th>
                    <th>{{ trans('ticket::admin.table-join-category') }}</th>
                    <th>{{ trans('ticket::admin.table-remove-agent') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($agents as $agent)
                <tr>
                    <td>
                        {{ $agent->id }}
                    </td>
                    <td>
                        {{ $agent->name }}
                    </td>
                    <td>
                        @foreach($agent->categories as $category)
                            <span style="color: {{ $category->color }}">
                                {{  $category->name }}
                            </span>
                        @endforeach
                    </td>
                    <td>
                        {!! CollectiveForm::open([
                                        'method' => 'PATCH',
                                        'route' => [
                                                    $setting->grab('admin_route').'.agent.update',
                                                    $agent->id
                                                    ],
                                        ]) !!}
                        @foreach(\mpba\tickets\Models\Category::all() as $agent_cat)
                            <input name="agent_cats[]"
                                   type="checkbox"
                                   class="form-check-input"
                                   value="{{ $agent_cat->id }}"
                                   {!! ($agent_cat->agents()->where("id", $agent->id)->count() > 0) ? "checked" : ""  !!}
                                   > {{ $agent_cat->name }}
                        @endforeach
                        {!! CollectiveForm::submit(trans('ticket::admin.btn-join'), ['class' => 'btn btn-info btn-sm']) !!}
                        {!! CollectiveForm::close() !!}
                    </td>
                    <td>
                        {!! CollectiveForm::open([
                        'method' => 'DELETE',
                        'route' => [
                                    $setting->grab('admin_route').'.agent.destroy',
                                    $agent->id
                                    ],
                        'id' => "delete-$agent->id"
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
