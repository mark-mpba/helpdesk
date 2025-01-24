@extends($master)

@section('page')
    {{ trans('ticket::admin.agent-index-title') }}
@stop

@section('content')
    @include('ticket::shared.header')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>{{ trans('ticket::admin.agent-index-title') }}
                {!! link_to_route(
                                    $setting->grab('admin_route').'.agent.create',
                                    trans('ticket::admin.btn-create-new-agent'), null,
                                    ['class' => 'btn btn-primary pull-right'])
                !!}
            </h2>
        </div>

        @if ($agents->isEmpty())
            <h3 class="text-center">{{ trans('ticket::admin.agent-index-no-agents') }}
                {!! link_to_route($setting->grab('admin_route').'.agent.create', trans('ticket::admin.agent-index-create-new')) !!}
            </h3>
        @else
            <div id="message"></div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <td>{{ trans('ticket::admin.table-id') }}</td>
                        <td>{{ trans('ticket::admin.table-name') }}</td>
                        <td>{{ trans('ticket::admin.table-categories') }}</td>
                        <td>{{ trans('ticket::admin.table-join-category') }}</td>
                        <td>{{ trans('ticket::admin.table-remove-agent') }}</td>
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
    </div>
@stop
