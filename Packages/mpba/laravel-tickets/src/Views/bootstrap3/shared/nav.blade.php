<div class="panel panel-default">
    <div class="panel-body">
        <ul class="nav nav-pills">
            <li role="presentation" class="{!! $tools->fullUrlIs(route(mpba\tickets\Models\Setting::grab('main_route') . '.index')) ? "active" : "" !!}">
                <a href="{{ route(mpba\ticket\Models\Setting::grab('main_route') . '.index') }}">{{ trans('ticket::lang.nav-active-tickets') }}
                    <span class="badge">
                         <?php
                            if ($u->isAdmin()) {
                                echo Kordy\ticket\Models\Ticket::active()->count();
                            } elseif ($u->isAgent()) {
                                echo Kordy\ticket\Models\Ticket::active()->agentUserTickets($u->id)->count();
                            } else {
                                echo Kordy\ticket\Models\Ticket::userTickets($u->id)->active()->count();
                            }
                        ?>
                    </span>
                </a>
            </li>
            <li role="presentation" class="{!! $tools->fullUrlIs(route(mpba\ticket\Models\Setting::grab('main_route') . '-complete')) ? "active" : "" !!}">
                <a href="{{ route(mpba\ticket\Models\Setting::grab('main_route') . '-complete') }}">{{ trans('ticket::lang.nav-completed-tickets') }}
                    <span class="badge">
                        <?php
                            if ($u->isAdmin()) {
                                echo mpba\ticket\Models\Ticket::complete()->count();
                            } elseif ($u->isAgent()) {
                                echo mpba\ticket\Models\Ticket::complete()->agentUserTickets($u->id)->count();
                            } else {
                                echo mpba\ticket\Models\Ticket::userTickets($u->id)->complete()->count();
                            }
                        ?>
                    </span>
                </a>
            </li>

            @if($u->isAdmin())
                <li role="presentation" class="{!! $tools->fullUrlIs(action('\mpba\tickets\Controllers\DashboardController@index')) || Request::is($setting->grab('admin_route').'/indicator*') ? "active" : "" !!}">
                    <a href="{{ action('\mpba\tickets\Controllers\DashboardController@index') }}">{{ trans('ticket::admin.nav-dashboard') }}</a>
                </li>

                <li role="presentation" class="dropdown {!!
                    $tools->fullUrlIs(action('\mpba\tickets\Controllers\StatusesController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\tickets\Controllers\PrioritiesController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\tickets\Controllers\AgentsController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\tickets\Controllers\CategoriesController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\tickets\Controllers\ConfigurationsController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\tickets\Controllers\AdministratorsController@index').'*')
                    ? "active" : "" !!}">

                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        {{ trans('ticket::admin.nav-settings') }} <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li role="presentation" class="{!! $tools->fullUrlIs(action('\mpba\tickets\Controllers\StatusesController@index').'*') ? "active" : "" !!}">
                            <a href="{{ action('\mpba\tickets\Controllers\StatusesController@index') }}">{{ trans('ticket::admin.nav-statuses') }}</a>
                        </li>
                        <li role="presentation"  class="{!! $tools->fullUrlIs(action('\mpba\tickets\Controllers\PrioritiesController@index').'*') ? "active" : "" !!}">
                            <a href="{{ action('\mpba\tickets\Controllers\PrioritiesController@index') }}">{{ trans('ticket::admin.nav-priorities') }}</a>
                        </li>
                        <li role="presentation"  class="{!! $tools->fullUrlIs(action('\mpba\tickets\Controllers\AgentsController@index').'*') ? "active" : "" !!}">
                            <a href="{{ action('\mpba\tickets\Controllers\AgentsController@index') }}">{{ trans('ticket::admin.nav-agents') }}</a>
                        </li>
                        <li role="presentation"  class="{!! $tools->fullUrlIs(action('\mpba\tickets\Controllers\CategoriesController@index').'*') ? "active" : "" !!}">
                            <a href="{{ action('\mpba\tickets\Controllers\CategoriesController@index') }}">{{ trans('ticket::admin.nav-categories') }}</a>
                        </li>
                        <li role="presentation"  class="{!! $tools->fullUrlIs(action('\mpba\tickets\Controllers\ConfigurationsController@index').'*') ? "active" : "" !!}">
                            <a href="{{ action('\mpba\tickets\Controllers\ConfigurationsController@index') }}">{{ trans('ticket::admin.nav-configuration') }}</a>
                        </li>
                        <li role="presentation"  class="{!! $tools->fullUrlIs(action('\mpba\tickets\Controllers\AdministratorsController@index').'*') ? "active" : "" !!}">
                            <a href="{{ action('\mpba\tickets\Controllers\AdministratorsController@index')}}">{{ trans('ticket::admin.nav-administrator') }}</a>
                        </li>
                    </ul>
                </li>
            @endif

        </ul>
    </div>
</div>
