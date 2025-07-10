<?php

use Illuminate\Support\Facades\Route;
use mpba\Tickets\Helpers\LaravelVersion;

Route::group(['middleware' => LaravelVersion::authMiddleware()],
    function () use ($main_route, $main_route_path, $admin_route, $admin_route_path) {

        // Ticket public route
        Route::get("$main_route_path/complete", 'mpba\Tickets\Controllers\TicketsController@indexComplete')
            ->name("$main_route-complete");
        Route::get("$main_route_path/archived", 'mpba\Tickets\Controllers\TicketsController@indexArchived')
            ->name("$main_route-archived");

        Route::get("$main_route_path/data/{id?}", 'mpba\Tickets\Controllers\TicketsController@data')
            ->name("$main_route.data");

        $field_name = last(explode('/', $main_route_path));
        Route::resource($main_route_path, '\mpba\Tickets\Controllers\TicketsController', [
            'names' => [
                'index' => $main_route . '.index',
                'store' => $main_route . '.store',
                'create' => $main_route . '.create',
                'update' => $main_route . '.update',
                'show' => $main_route . '.show',
                'destroy' => $main_route . '.destroy',
                'edit' => $main_route . '.edit',
                'archived' => $main_route . '.archived',

            ],
            'parameters' => [
                $field_name => 'ticket',
            ],
        ]);

        // Tests Comments public route
        Route::resource('/tests', '\mpba\Tickets\Controllers\TestsController', [
            'names' => [
                'index' => 'tests.index',
                'store' => 'test.store',
                'create' => 'test.create',
                'update' => 'test.update',
                'show' => 'test.show',
                'destroy' => 'test.destroy',
                'edit' => 'test.edit',
            ],
            'parameters' => [
                $field_name => 'test',
            ],
        ]);

        // Ticket Comments public route
        $field_name = last(explode('/', "$main_route_path-comment"));
        Route::resource("$main_route_path-comment", '\mpba\Tickets\Controllers\CommentsController', [
            'names' => [
                'index' => "$main_route-comment.index",
                'store' => "$main_route-comment.store",
                'create' => "$main_route-comment.create",
                'update' => "$main_route-comment.update",
                'show' => "$main_route-comment.show",
                'destroy' => "$main_route-comment.destroy",
                'edit' => "$main_route-comment.edit",
            ],
            'parameters' => [
                $field_name => 'ticket_comment',
            ],
        ]);

        // Ticket complete route for permitted user.
        Route::get("$main_route_path/{id}/complete", '\mpba\Tickets\Controllers\TicketsController@complete')
            ->name("$main_route.complete");

        Route::get("$main_route_path/{id}/archived", '\mpba\Tickets\Controllers\TicketsController@archived')
            ->name("$main_route.archived");

        // Ticket reopen route for permitted user.
        Route::get("$main_route_path/{id}/reopen", '\mpba\Tickets\Controllers\TicketsController@reopen')
            ->name("$main_route.reopen");
        //});

        Route::group(['middleware' => '\mpba\Tickets\Middleware\IsAgentMiddleware'],
            function () use ($main_route, $main_route_path) {
                //API return list of agents in particular category
                Route::get("$main_route_path/agents/list/{category_id?}/{ticket_id?}", [
                    'as' => $main_route . 'agentselectlist',
                    'uses' => '\mpba\Tickets\Controllers\TicketsController@agentSelectList',
                ]);
            });

        Route::group(['middleware' => '\mpba\Tickets\Middleware\IsAdminMiddleware'],
            function () use ($admin_route, $admin_route_path) {

                Route::get("$admin_route_path/indicator/{indicator_period?}", [
                    'as' => $admin_route . '.dashboard.indicator',
                    'uses' => '\mpba\Tickets\Controllers\DashboardController@index',
                ]);

                Route::get($admin_route_path, '\mpba\Tickets\Controllers\DashboardController@index');

                Route::resource("$admin_route_path/status", '\mpba\Tickets\Controllers\StatusesController', [
                    'names' => [
                        'index' => "$admin_route.status.index",
                        'store' => "$admin_route.status.store",
                        'create' => "$admin_route.status.create",
                        'update' => "$admin_route.status.update",
                        'show' => "$admin_route.status.show",
                        'destroy' => "$admin_route.status.destroy",
                        'edit' => "$admin_route.status.edit",
                    ],
                ]);

                Route::resource("$admin_route_path/priority", '\mpba\Tickets\Controllers\PrioritiesController', [
                    'names' => [
                        'index' => "$admin_route.priority.index",
                        'store' => "$admin_route.priority.store",
                        'create' => "$admin_route.priority.create",
                        'update' => "$admin_route.priority.update",
                        'show' => "$admin_route.priority.show",
                        'destroy' => "$admin_route.priority.destroy",
                        'edit' => "$admin_route.priority.edit",
                    ],
                ]);

                //Agents management routes (ex. http://url/tickets-admin/agent)
                Route::resource("$admin_route_path/agent", '\mpba\Tickets\Controllers\AgentsController', [
                    'names' => [
                        'index' => "$admin_route.agent.index",
                        'store' => "$admin_route.agent.store",
                        'create' => "$admin_route.agent.create",
                        'update' => "$admin_route.agent.update",
                        'show' => "$admin_route.agent.show",
                        'destroy' => "$admin_route.agent.destroy",
                        'edit' => "$admin_route.agent.edit",
                    ],
                ]);

                //Agents management routes (ex. http://url/tickets-admin/agent)
                Route::resource("$admin_route_path/category", '\mpba\Tickets\Controllers\CategoriesController', [
                    'names' => [
                        'index' => "$admin_route.category.index",
                        'store' => "$admin_route.category.store",
                        'create' => "$admin_route.category.create",
                        'update' => "$admin_route.category.update",
                        'show' => "$admin_route.category.show",
                        'destroy' => "$admin_route.category.destroy",
                        'edit' => "$admin_route.category.edit",
                    ],
                ]);

                //Settings configuration routes (ex. http://url/tickets-admin/configuration)
                Route::resource("$admin_route_path/configuration", '\mpba\Tickets\Controllers\ConfigurationsController',
                    [
                        'names' => [
                            'index' => "$admin_route.configuration.index",
                            'store' => "$admin_route.configuration.store",
                            'create' => "$admin_route.configuration.create",
                            'update' => "$admin_route.configuration.update",
                            'show' => "$admin_route.configuration.show",
                            'destroy' => "$admin_route.configuration.destroy",
                            'edit' => "$admin_route.configuration.edit",
                        ],
                    ]);

                //Administrators configuration routes (ex. http://url/tickets-admin/administrators)
                Route::resource("$admin_route_path/administrator", 'mpba\Tickets\Controllers\AdministratorsController',
                    [
                        'names' => [
                            'index' => "$admin_route.administrator.index",
                            'store' => "$admin_route.administrator.store",
                            'create' => "$admin_route.administrator.create",
                            'update' => "$admin_route.administrator.update",
                            'show' => "$admin_route.administrator.show",
                            'destroy' => "$admin_route.administrator.destroy",
                            'edit' => "$admin_route.administrator.edit",
                        ],
                    ]);

                Route::resource("$admin_route_path/project", 'mpba\Tickets\Controllers\ProjectsController', [
                    'names' => [
                        'index' => "$admin_route.projects.index",
                        'store' => "$admin_route.project.store",
                        'create' => "$admin_route.project.create",
                        'update' => "$admin_route.project.update",
                        'show' => "$admin_route.project.show",
                        'destroy' => "$admin_route.project.destroy",
                        'edit' => "$admin_route.project.edit",
                    ],
                ]);
            });
    });
