<?php

namespace mpba\Tickets\Middleware;

use Closure;
use Illuminate\Http\Request;
use mpba\Tickets\Models\Agent;
use mpba\Tickets\Models\Setting;

class IsAgentMiddleware
{
    /**
     * Run the request filter.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Agent::isAgent() || Agent::isAdmin()) {
            return $next($request);
        }

        return redirect()->route(Setting::grab('main_route').'.index')
            ->with('warning', trans('ticket::lang.you-are-not-permitted-to-access'));
    }
}
