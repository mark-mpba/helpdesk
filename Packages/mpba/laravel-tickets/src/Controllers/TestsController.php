<?php

namespace mpba\Tickets\Controllers;

use App\Http\Controllers\Controller;
use Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use mpba\Tickets\Models;
use mpba\Tickets\Models\Setting;
use mpba\Tickets\Models\Test;

class TestsController extends Controller
{
    protected $tests;


    public function __construct(Models\Test $tests)
    {
        $this->tests = $tests;
    }

    /**
     * Display a listing of active tickets related to user.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(Test::all())
                ->addColumn('passed', function ($row) {
                    // Return formatted status if needed
                    if (!empty($row->passed)) {
                        return $row->passed ? 'Pass' : 'Fail';
                    } else {
                        return "Awaiting Test";
                    }
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d-M-Y') : '';
                })
                ->make(true);
        }
        return view('ticket::tests.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {

        return view('ticket::tests.create',
            []
        );
        //compact('priorities', 'categories','projects'));
    }

    /**
     * Store a newly created ticket and auto assign an agent for it.
     *
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $test = new Test();
        $test->step = $request->step;
        $test->name = $request->name;
        $test->details = $request->details;
        $test->outcome = $request->outcome;
        $test->actual = $request->actual;
        $test->save();
        session()->flash('status', 'Test created successfully');

        return redirect()->route('tests.index');
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


}
