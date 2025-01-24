<?php

namespace mpba\Tickets\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use mpba\Tickets\Models;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('mpba\Tickets\Middleware\IsAdminMiddleware', ['only' => ['edit', 'update', 'destroy']]);
        $this->middleware('mpba\Tickets\Middleware\ResAccessMiddleware', ['only' => 'store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'ticket_id' => 'required|exists:tickets,id',
            'content' => 'required|min:6',
        ]);

        $comment = new Models\Comment();

        $comment->setPurifiedContent($request->get('content'));

        $comment->ticket_id = $request->get('ticket_id');
        $comment->user_id = \Auth::user()->id;
        $comment->save();

        $ticket = Models\Ticket::find($comment->ticket_id);
        $ticket->updated_at = $comment->created_at;
        $ticket->save();

        return back()->with('status', trans('ticket::lang.comment-has-been-added-ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
