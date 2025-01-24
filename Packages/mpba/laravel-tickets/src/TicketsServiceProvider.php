<?php

namespace mpba\Tickets;

use Collective\Html\FormFacade as CollectiveForm;
use Collective\Html\HtmlServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Date\DateServiceProvider;
use Mews\Purifier\PurifierServiceProvider;
use mpba\Tickets\Console\Htmlify;
use mpba\Tickets\Controllers\InstallController;
use mpba\Tickets\Controllers\NotificationsController;
use mpba\Tickets\Helpers\LaravelVersion;
use mpba\Tickets\Models\Comment;
use mpba\Tickets\Models\Setting;
use mpba\Tickets\Models\Ticket;
use mpba\Tickets\ViewComposers\TicketsComposer;
use Yajra\DataTables\DataTablesServiceProvider;

class TicketsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! Schema::hasTable('migrations')) {
            // Database isn't installed yet so bail.
            return;
        }
        $installer = new InstallController();

        // if a migration or new setting is missing scape to the installation
        if (empty($installer->inactiveMigrations()) && ! $installer->inactiveSettings()) {
            // Send the Agent User model to the view under $u
            // The send settings to views under $setting
            $u = null;

            TicketsComposer::settings($u);

            // Adding HTML5 color picker for form elements
            CollectiveForm::macro('custom', function ($type, $name, $value = '#000000', $options = []) {
                $field = $this->input($type, $name, $value, $options);

                return $field;
            });

            TicketsComposer::general();
            TicketsComposer::codeMirror();
            TicketsComposer::sharedAssets();
            TicketsComposer::summerNotes();

            // Send notification when new comment is added
            Comment::creating(function ($comment) {
                if (Setting::grab('comment_notification')) {
                    $notification = new NotificationsController();
                    $notification->newComment($comment);
                }
            });

            // Send notification when ticket status is modified
            Ticket::updating(function ($modified_ticket) {
                if (Setting::grab('status_notification')) {
                    $original_ticket = Ticket::find($modified_ticket->id);
                    if ($original_ticket->status_id != $modified_ticket->status_id || $original_ticket->completed_at != $modified_ticket->completed_at) {
                        $notification = new NotificationsController();
                        $notification->ticketStatusUpdated($modified_ticket, $original_ticket);
                    }
                }
                if (Setting::grab('assigned_notification')) {
                    $original_ticket = Ticket::find($modified_ticket->id);
                    if ($original_ticket->agent->id != $modified_ticket->agent->id) {
                        $notification = new NotificationsController();
                        $notification->ticketAgentUpdated($modified_ticket, $original_ticket);
                    }
                }

                return true;
            });

            // Send notification when ticket status is modified
            Ticket::created(function ($ticket) {
                if (Setting::grab('assigned_notification')) {
                    $notification = new NotificationsController();
                    $notification->newTicketNotifyAgent($ticket);
                }

                return true;
            });

            $this->loadTranslationsFrom(__DIR__.'/Translations', 'ticket');

            $viewsDirectory = __DIR__.'/Views/bootstrap3';
            if (Setting::grab('bootstrap_version') == '4') {
                $viewsDirectory = __DIR__.'/Views/bootstrap4';
            }

            $this->loadViewsFrom($viewsDirectory, 'ticket');

            $this->publishes([$viewsDirectory => base_path('resources/views/vendor/tickets')], 'views');
            $this->publishes([__DIR__.'/Translations' => base_path('resources/lang/vendor/tickets')], 'lang');
            $this->publishes([__DIR__.'/Public' => public_path('vendor/tickets')], 'public');
            $this->publishes([__DIR__.'/Migrations' => base_path('database/migrations')], 'db');

            // Check public assets are present, publish them if not
//            $installer->publicAssets();

            $main_route = Setting::grab('main_route');
            $main_route_path = Setting::grab('main_route_path');
            $admin_route = Setting::grab('admin_route');
            $admin_route_path = Setting::grab('admin_route_path');

            if (file_exists(Setting::grab('routes'))) {
                include Setting::grab('routes');
            } else {
                include __DIR__.'/routes.php';
            }
        } elseif (Request::path() == 'tickets-install'
                || Request::path() == 'tickets-upgrade'
                || Request::path() == 'tickets'
                || Request::path() == 'tickets-admin'
                || (isset($_SERVER['ARTISAN_TICKETS_INSTALLING']) && $_SERVER['ARTISAN_TICKETS_INSTALLING'])) {
            $this->loadTranslationsFrom(__DIR__.'/Translations', 'ticket');
            $this->loadViewsFrom(__DIR__.'/Views/bootstrap3', 'ticket');
            $this->publishes([__DIR__.'/Migrations' => base_path('database/migrations')], 'db');

            $authMiddleware = Helpers\LaravelVersion::authMiddleware();

            Route::get('/tickets-install', [
                'middleware' => $authMiddleware,
                'as' => 'tickets.install.index',
                'uses' => 'mpba\Tickets\Controllers\InstallController@index',
            ]);
            Route::post('/tickets-install', [
                'middleware' => $authMiddleware,
                'as' => 'tickets.install.setup',
                'uses' => 'mpba\Tickets\Controllers\InstallController@setup',
            ]);
            Route::get('/tickets-upgrade', [
                'middleware' => $authMiddleware,
                'as' => 'tickets.install.upgrade',
                'uses' => 'mpba\Tickets\Controllers\InstallController@upgrade',
            ]);
            Route::get('/tickets', function () {
                return redirect()->route('tickets.install.index');
            });
            Route::get('/tickets-admin', function () {
                return redirect()->route('tickets.install.index');
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Register the service provider for the dependency.
         */
        $this->app->register(HtmlServiceProvider::class);

        if (LaravelVersion::min('5.4')) {
            $this->app->register(DataTablesServiceProvider::class);
        } else {
            $this->app->register(DatatablesServiceProvider::class);
        }

        $this->app->register(DateServiceProvider::class);
        $this->app->register(PurifierServiceProvider::class);
        /*
         * Create aliases for the dependency.
         */
        $loader = AliasLoader::getInstance();
        $loader->alias('CollectiveForm', 'Collective\Html\FormFacade');

        /*
         * Register htmlify command. Need to run this when upgrading from <=0.2.2
         */

        $this->app->singleton('command.mpba.tickets.htmlify', function ($app) {
            return new Htmlify();
        });
        $this->commands('command.mpba.tickets.htmlify');
    }
}
