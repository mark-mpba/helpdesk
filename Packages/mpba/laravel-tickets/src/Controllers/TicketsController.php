<?php

namespace mpba\Tickets\Controllers;

use App\Http\Controllers\Controller;
use Cache;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use mpba\Tickets\Helpers\LaravelVersion;
use mpba\Tickets\Models;
use mpba\Tickets\Models\Agent;
use mpba\Tickets\Models\Category;
use mpba\Tickets\Models\Setting;
use mpba\Tickets\Models\Ticket;
use mpba\Tickets\Requests\TicketRequest;

class TicketsController extends Controller
{
    protected $tickets;

    protected $agent;

    public function __construct(Ticket $tickets, Agent $agent)
    {
        $this->middleware('mpba\Tickets\Middleware\ResAccessMiddleware', ['only' => ['show']]);
        $this->middleware('mpba\Tickets\Middleware\IsAgentMiddleware', ['only' => ['edit', 'update']]);
        $this->middleware('mpba\Tickets\Middleware\IsAdminMiddleware', ['only' => ['destroy']]);

        $this->tickets = $tickets;
        $this->agent = $agent;
    }

    public function data(Request $request)
    {

        $complete = $request->get('complete');
        $archived = $request->get('archived');

        if (LaravelVersion::min('5.4')) {
            $datatables = app(\Yajra\DataTables\DataTables::class);
        } else {
            $datatables = app(\Yajra\Datatables\Datatables::class);
        }

        $user = $this->agent->find(auth()->user()->id);

        if ($user->isAdmin()) {
            if ($complete) {
                $collection = Ticket::complete();
            } else {
                $collection = Ticket::active();
            }
        } elseif ($user->isAgent()) {
            if ($complete) {
                $collection = Ticket::complete()->agentUserTickets($user->id);
            } else {
                $collection = Ticket::active()->agentUserTickets($user->id);
            }
        } else {
            if ($complete) {
                $collection = Ticket::userTickets($user->id)->complete();
            } else {
                $collection = Ticket::userTickets($user->id)->active();
            }
        }

        $collection
            ->join('users', 'users.id', '=', 'tickets.user_id')
            ->join('tickets_statuses', 'tickets_statuses.id', '=', 'tickets.status_id')
            ->join('tickets_priorities', 'tickets_priorities.id', '=', 'tickets.priority_id')
            ->join('tickets_categories', 'tickets_categories.id', '=', 'tickets.category_id')
            ->join('tickets_projects', 'tickets_projects.id', '=', 'tickets.project_id')
            ->select([
                'tickets.id',
                'tickets.reference',
                'tickets.subject AS subject',
                'tickets_projects.title AS project',
                'tickets_statuses.name AS status',
                'tickets_statuses.color AS color_status',
                'tickets_priorities.color AS color_priority',
                'tickets_categories.color AS color_category',
                'tickets.id AS agent',
                'tickets.updated_at AS updated_at',
                'tickets_priorities.name AS priority',
                'users.name AS owner',
                'tickets.agent_id',
                'tickets_categories.name AS category',
            ]);


        $collection = $datatables->of($collection);

        $this->renderTicketTable($collection);

        $collection->editColumn('updated_at', '{!! \Carbon\Carbon::parse($updated_at)->diffForHumans() !!}');

        // method rawColumns was introduced in laravel-datatables 7, which is only compatible with >L5.4
        // in previous laravel-datatables versions escaping columns wasn't default.

        if (LaravelVersion::min('5.4')) {
            $collection->rawColumns(['subject', 'status', 'priority', 'category', 'agent']);
        }

        return $collection->make(true);
    }

    public function renderTicketTable($collection)
    {
        $collection->editColumn('subject', function ($ticket) {
            //return substr($ticket->subject,0,45);
            return (string)link_to_route(
                Setting::grab('main_route') . '.show',
                substr($ticket->subject, 0, 45),
                $ticket->id
            );
        });

        $collection->editColumn('status', function ($ticket) {
            $color = $ticket->color_status;
            $status = e($ticket->status);

            return "<div style='color: $color'>$status</div>";
        });

        $collection->editColumn('priority', function ($ticket) {
            $color = $ticket->color_priority;
            $priority = e($ticket->priority);

            return "<div style='color: $color'>$priority</div>";
        });

        $collection->editColumn('category', function ($ticket) {
            $color = $ticket->color_category;
            $category = e($ticket->category);

            return "<div style='color: $color'>$category</div>";
        });

        $collection->editColumn('agent', function ($ticket) {
            $ticket = $this->tickets->find($ticket->id);

            return e($ticket->agent->name);
        });

        return $collection;
    }

    /**
     * Display a listing of active tickets related to user.
     *
     * @return Response
     */
    public function index()
    {
        $complete = false;
        $archived = false;
        return view('ticket::index', compact('complete', 'archived'));
    }

    /**
     * Display a listing of completed tickets related to user.
     *
     * @return Response
     */
    public function indexComplete()
    {
        $complete = true;
        $archived = false;
        return view('ticket::index', compact('complete', 'archived'));
    }

    /**
     * Display a listing of archived tickets related to user.
     *
     * @return Response
     */
    public function indexArchived()
    {
        $archived = true;
        $complete = false;

        return view('ticket::index', compact('complete', 'archived'));
    }

    /**
     * Returns priorities, categories and statuses lists in this order
     * Decouple it with list().
     *
     * @return array
     */
    protected function PCS()
    {
        // seconds expected for L5.8<=, minutes before that
        $time = LaravelVersion::min('5.8') ? 60 * 60 : 60;

        $priorities = Cache::remember('ticket::priorities', $time, function () {
            return Models\Priority::all();
        });

        $categories = Cache::remember('ticket::categories', $time, function () {
            return Models\Category::all();
        });

        $statuses = Cache::remember('ticket::statuses', $time, function () {
            return Models\Status::all();
        });

        $projects = Cache::remember('ticket::projects', $time, function () {
            return Models\Project::all();
        });

        if (LaravelVersion::min('5.3.0')) {
            return [
                $priorities->pluck('name', 'id'),
                $categories->pluck('name', 'id'),
                $projects->pluck('title', 'id'),
                $statuses->pluck('name', 'id')
            ];
        } else {
            return [
                $priorities->lists('name', 'id'),
                $categories->lists('name', 'id'),
                $projects->pluck('title', 'id'),
                $statuses->lists('name', 'id'),
            ];
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        [$priorities, $categories, $projects] = $this->PCS();

        return view('ticket::tickets.create',
            [
                'priorities' => $priorities,
                'categories' => $categories,
                'projects' => $projects
            ]
        );
        //compact('priorities', 'categories','projects'));
    }

    /**
     * Store a newly created ticket and auto assign an agent for it.
     *
     *
     * @return RedirectResponse
     */
    public function store(TicketRequest $request)
    {
        $ticket = new Ticket();
        $ticket->subject = $request->subject;
        $ticket->setPurifiedContent($request->get('content'));

        $ticket->priority_id = $request->priority_id;
        $ticket->category_id = $request->category_id;
        $ticket->project_id = $request->project_id;
        $ticket->reference = $request->reference;
        $ticket->status_id = Setting::grab('default_status_id');
        $ticket->user_id = auth()->user()->id;

        //$ticket->autoSelectAgent();
        $ticket->agent_id = auth()->user()->id;
        $ticket->save();

        session()->flash('status', trans('ticket::lang.the-ticket-has-been-created'));

        return redirect()->route(Setting::grab('main_route') . '.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $ticket = $this->tickets->findOrFail($id);

        [$priority_lists, $category_lists, $project_lists, $status_lists] = $this->PCS();

        $close_perm = $this->permToClose($id);
        $reopen_perm = $this->permToReopen($id);

        $cat_agents = Models\Category::find($ticket->category_id)->agents()->agentsLists();
        if (is_array($cat_agents)) {
            $agent_lists = ['auto' => 'Auto Select'] + $cat_agents;
        } else {
            $agent_lists = ['auto' => 'Auto Select'];
        }

        $comments = $ticket->comments()->paginate(Setting::grab('paginate_items'));

        return view('ticket::tickets.show',
            compact('ticket', 'project_lists', 'status_lists', 'priority_lists', 'category_lists', 'agent_lists',
                'comments',
                'close_perm', 'reopen_perm'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'subject' => 'required|min:3',
            'content' => 'required|min:6',
            'priority_id' => 'required|exists:tickets_priorities,id',
            'category_id' => 'required|exists:tickets_categories,id',
            'status_id' => 'required|exists:tickets_statuses,id',
            'agent_id' => 'required',
        ]);

        $ticket = $this->tickets->findOrFail($id);

        $ticket->subject = $request->subject;

        $ticket->setPurifiedContent($request->get('content'));

        $ticket->status_id = $request->status_id;
        $ticket->category_id = $request->category_id;
        $ticket->priority_id = $request->priority_id;

        if ($request->input('agent_id') == 'auto') {
            $ticket->autoSelectAgent();
        } else {
            $ticket->agent_id = $request->input('agent_id');
        }

        $ticket->save();

        session()->flash('status', trans('ticket::lang.the-ticket-has-been-modified'));

        return redirect()->route(Setting::grab('main_route') . '.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $ticket = $this->tickets->findOrFail($id);
        $subject = $ticket->subject;
        $ticket->delete();

        session()->flash('status', trans('ticket::projects.has-been-deleted', ['name' => $subject]));

        return redirect()->route(Setting::grab('main_route') . '.index');
    }

    /**
     * Mark ticket as complete.
     *
     * @param int $id
     * @return Response
     */
    public function complete($id)
    {
        if ($this->permToClose($id) == 'yes') {
            $ticket = $this->tickets->findOrFail($id);
            $ticket->completed_at = Carbon::now();

            if (Setting::grab('default_close_status_id')) {
                $ticket->status_id = Setting::grab('default_close_status_id');
            }

            $subject = $ticket->subject;
            $ticket->save();

            session()->flash('status', trans('ticket::lang.the-ticket-has-been-completed', ['name' => $subject]));

            return redirect()->route(Setting::grab('main_route') . '.index');
        }

        return redirect()->route(Setting::grab('main_route') . '.index')
            ->with('warning', trans('ticket::lang.you-are-not-permitted-to-do-this'));
    }

    /**
     * Reopen ticket from complete status.
     *
     * @param int $id
     * @return Response
     */
    public function reopen($id)
    {
        if ($this->permToReopen($id) == 'yes') {
            $ticket = $this->tickets->findOrFail($id);
            $ticket->completed_at = null;

            if (Setting::grab('default_reopen_status_id')) {
                $ticket->status_id = Setting::grab('default_reopen_status_id');
            }

            $subject = $ticket->subject;
            $ticket->save();

            session()->flash('status', trans('ticket::lang.the-ticket-has-been-reopened', ['name' => $subject]));

            return redirect()->route(Setting::grab('main_route') . '.index');
        }

        return redirect()->route(Setting::grab('main_route') . '.index')
            ->with('warning', trans('ticket::lang.you-are-not-permitted-to-do-this'));
    }

    public function agentSelectList($category_id, $ticket_id)
    {
        $cat_agents = Models\Category::find($category_id)->agents()->agentsLists();
        if (is_array($cat_agents)) {
            $agents = ['auto' => 'Auto Select'] + $cat_agents;
        } else {
            $agents = ['auto' => 'Auto Select'];
        }

        $selected_Agent = $this->tickets->find($ticket_id)->agent->id;
        $select = '<select class="form-control" id="agent_id" name="agent_id">';
        foreach ($agents as $id => $name) {
            $selected = ($id == $selected_Agent) ? 'selected' : '';
            $select .= '<option value="' . $id . '" ' . $selected . '>' . $name . '</option>';
        }
        $select .= '</select>';

        return $select;
    }

    /**
     * @return bool
     */
    public function permToClose($id)
    {
        $close_ticket_perm = Setting::grab('close_ticket_perm');

        if ($this->agent->isAdmin() && $close_ticket_perm['admin'] == 'yes') {
            return 'yes';
        }
        if ($this->agent->isAgent() && $close_ticket_perm['agent'] == 'yes') {
            return 'yes';
        }
        if ($this->agent->isTicketOwner($id) && $close_ticket_perm['owner'] == 'yes') {
            return 'yes';
        }

        return 'no';
    }

    /**
     * @return bool
     */
    public function permToReopen($id)
    {
        $reopen_ticket_perm = Setting::grab('reopen_ticket_perm');
        if ($this->agent->isAdmin() && $reopen_ticket_perm['admin'] == 'yes') {
            return 'yes';
        } elseif ($this->agent->isAgent() && $reopen_ticket_perm['agent'] == 'yes') {
            return 'yes';
        } elseif ($this->agent->isTicketOwner($id) && $reopen_ticket_perm['owner'] == 'yes') {
            return 'yes';
        }

        return 'no';
    }

    /**
     * Calculate average closing period of days per category for number of months.
     *
     * @param int $period
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function monthlyPerfomance($period = 2)
    {
        $categories = Category::all();
        foreach ($categories as $cat) {
            $records['categories'][] = $cat->name;
        }

        for ($m = $period; $m >= 0; $m--) {
            $from = Carbon::now();
            $from->day = 1;
            $from->subMonth($m);
            $to = Carbon::now();
            $to->day = 1;
            $to->subMonth($m);
            $to->endOfMonth();
            $records['interval'][$from->format('F Y')] = [];
            foreach ($categories as $cat) {
                $records['interval'][$from->format('F Y')][] = round($this->intervalPerformance($from, $to, $cat->id),
                    1);
            }
        }

        return $records;
    }

    /**
     * Calculate the date length it took to solve a ticket.
     *
     * @param Ticket $ticket
     * @return int|false
     */
    public function ticketPerformance($ticket)
    {
        if ($ticket->completed_at == null) {
            return false;
        }

        $created = new Carbon($ticket->created_at);
        $completed = new Carbon($ticket->completed_at);
        $length = $created->diff($completed)->days;

        return $length;
    }

    /**
     * Calculate the average date length it took to solve tickets within date period.
     *
     *
     * @return int
     */
    public function intervalPerformance($from, $to, $cat_id = false)
    {
        if ($cat_id) {
            $tickets = ticket::where('category_id', $cat_id)->whereBetween('completed_at', [$from, $to])->get();
        } else {
            $tickets = ticket::whereBetween('completed_at', [$from, $to])->get();
        }

        if (empty($tickets->first())) {
            return false;
        }

        $performance_count = 0;
        $counter = 0;
        foreach ($tickets as $ticket) {
            $performance_count += $this->ticketPerformance($ticket);
            $counter++;
        }
        $performance_average = $performance_count / $counter;

        return $performance_average;
    }
}
