<nav>
    <ul class="nav nav-pills">
        <li role="presentation" class="nav-item">
            <a class="nav-link {!! $tools->fullUrlIs(route(mpba\tickets\Models\Setting::grab('main_route') . '.index')) ? "active" : "" !!}"
               href="{{ route(mpba\tickets\Models\Setting::grab('main_route') . '.index') }}">{{ trans('ticket::nav.active-tickets') }}
                <span class="badge badge-pill badge-danger ">
                     <?php
                     if ($u->isAdmin()) {
                         echo mpba\tickets\Models\Ticket::active()->count();
                     } elseif ($u->isAgent()) {
                         echo mpba\tickets\Models\Ticket::active()->agentUserTickets($u->id)->count();
                     } else {
                         echo mpba\tickets\Models\Ticket::userTickets($u->id)->active()->count();
                     }
                     ?>
                </span>
            </a>
        </li>

        <li style="margin-left: 5px;" role="presentation" class="nav-item ">
            <a class="nav-link {{ request()->is('tests*')?'active':'' }} "
               href="{{ route('tests.index') }}">System Test Scripts
                <span class="badge badge-pill badge-danger ">
                     <?php
                     if ($u->isAdmin()) {
                         echo mpba\Tickets\Models\Test::all()->count();
                     }
                     ?>
                </span>
            </a>
        </li>

        <li role="presentation" class="nav-item">
            <a class="nav-link {!! $tools->fullUrlIs(route(mpba\tickets\Models\Setting::grab('main_route') . '-complete')) ? "active" : "" !!}"
               href="{{ route(mpba\tickets\Models\Setting::grab('main_route') . '-complete') }}">{{ trans('ticket::nav.completed-tickets') }}
                <span class="badge badge-pill badge-danger">
                    <?php
                    if ($u->isAdmin()) {
                        echo mpba\tickets\Models\Ticket::complete()->count();
                    } elseif ($u->isAgent()) {
                        echo mpba\tickets\Models\Ticket::complete()->agentUserTickets($u->id)->count();
                    } else {
                        echo mpba\tickets\Models\Ticket::userTickets($u->id)->complete()->count();
                    }
                    ?>
                </span>
            </a>
        </li>
        <li role="presentation" class="nav-item" style="display: none;">
            <a class="nav-link {!! $tools->fullUrlIs(route(mpba\tickets\Models\Setting::grab('main_route') . '-archived')) ? "active" : "" !!}"
               href="{{ route(mpba\tickets\Models\Setting::grab('main_route') . '-archived') }}">{{ trans('ticket::nav.archived-tickets') }}
                <span class="badge badge-pill badge-secondary">
                    <?php
                    if ($u->isAdmin()) {
                        echo mpba\tickets\Models\Ticket::onlyArchived()->count();
                    } elseif ($u->isAgent()) {
                        echo mpba\tickets\Models\Ticket::complete()->agentUserTickets($u->id)->count();
                    } else {
                        echo mpba\tickets\Models\Ticket::userTickets($u->id)->complete()->count();
                    }
                    ?>
                </span>
            </a>
        </li>
        @if($u->isAdmin())
            <li role="presentation" class="nav-item">
                <a class="nav-link {!! $tools->fullUrlIs(action('\mpba\Tickets\Controllers\DashboardController@index')) || Request::is($setting->grab('admin_route').'/indicator*') ? "active" : "" !!}"
                   href="{{ action('\mpba\Tickets\Controllers\DashboardController@index') }}">{{ trans('ticket::nav.dashboard') }}</a>
            </li>

            <li role="presentation" class="nav-item dropdown">

                <a class="nav-link dropdown-toggle {!!
                    $tools->fullUrlIs(action('\mpba\Tickets\Controllers\StatusesController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\Tickets\Controllers\PrioritiesController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\Tickets\Controllers\AgentsController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\Tickets\Controllers\CategoriesController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\Tickets\Controllers\ConfigurationsController@index').'*') ||
                    $tools->fullUrlIs(action('\mpba\Tickets\Controllers\AdministratorsController@index').'*')
                    ? "active" : "" !!}"
                   data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    {{ trans('ticket::nav.settings') }}
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item {!! $tools->fullUrlIs(action('\mpba\Tickets\Controllers\StatusesController@index').'*') ? "active" : "" !!}"
                       href="{{ action('\mpba\Tickets\Controllers\StatusesController@index') }}">{{ trans('ticket::nav.statuses') }}</a>

                    <a class="dropdown-item {!! $tools->fullUrlIs(action('\mpba\Tickets\Controllers\PrioritiesController@index').'*') ? "active" : "" !!}"
                       href="{{ action('\mpba\Tickets\Controllers\PrioritiesController@index') }}">{{ trans('ticket::nav.priorities') }}</a>

                    <a class="dropdown-item {!! $tools->fullUrlIs(action('\mpba\Tickets\Controllers\AgentsController@index').'*') ? "active" : "" !!}"
                       href="{{ action('\mpba\Tickets\Controllers\AgentsController@index') }}">{{ trans('ticket::nav.agents') }}</a>

                    <a class="dropdown-item {!! $tools->fullUrlIs(action('\mpba\Tickets\Controllers\CategoriesController@index').'*') ? "active" : "" !!}"
                       href="{{ action('\mpba\Tickets\Controllers\CategoriesController@index') }}">{{ trans('ticket::nav.categories') }}</a>

                    <a class="dropdown-item {!! $tools->fullUrlIs(action('\mpba\Tickets\Controllers\ConfigurationsController@index').'*') ? "active" : "" !!}"
                       href="{{ action('\mpba\Tickets\Controllers\ConfigurationsController@index') }}">{{ trans('ticket::nav.configuration') }}</a>

                    <a class="dropdown-item {!! $tools->fullUrlIs(action('\mpba\Tickets\Controllers\AdministratorsController@index').'*') ? "active" : "" !!}"
                       href="{{ action('\mpba\Tickets\Controllers\AdministratorsController@index')}}">{{ trans('ticket::nav.administrator') }}</a>

                    <a class="dropdown-item {!! $tools->fullUrlIs(action('\mpba\Tickets\Controllers\ProjectsController@index').'*') ? "active" : "" !!}"
                       href="{{ action('\mpba\Tickets\Controllers\ProjectsController@index')}}">{{ trans('ticket::nav.projects') }}</a>

                </div>
            </li>
        @endif

    </ul>
</nav>
