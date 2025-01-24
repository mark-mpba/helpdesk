<?php

namespace mpba\Tickets\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use mpba\Tickets\Helpers\LaravelVersion;
use mpba\Tickets\Models\Priority;
use mpba\Tickets\Models\Project;
use mpba\Tickets\Models\Projects;
use mpba\Tickets\Requests\StoreProjectsRequest;
use mpba\Tickets\Requests\UpdateProjectsRequest;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $time = LaravelVersion::min('5.8') ? 60 * 60 : 60;
        $projects = \Cache::remember('ticket::projects', $time, function () {
            return Project::all();
        });

        return view('ticket::admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('ticket::admin.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProjectsRequest $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'color' => 'required',
        ]);

        //$project = new Project();
        $project = Project::Create(['title' => $request->title, 'color' => $request->color]);

        Session::flash('status', trans('ticket::project-name-has-been-created', ['name' => $request->name]));

        \Cache::forget('ticket::projects');

        return redirect()->action('\mpba\Tickets\Controllers\ProjectsController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  Projects  $projects
     * @return Response
     */
    public function show($id)
    {
        return trans('ticket::projects.project-all-tickets-here');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Project  $project
     * @return Response
     */
    public function edit(Project $project)
    {
        $project = Project::findOrFail($project->id);

        return view('ticket::admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \mpba\Tickets\Requests\UpdateProjectsRequest  $request
     * @param  Projects  $projects
     * @return Response
     */
    public function update(UpdateProjectsRequest $request, Project $project)
    {

        $this->validate($request, [
            'title' => 'required',
            'color' => 'required',
        ]);

        $project = Project::findOrFail($project->id);
        $project->update(['title' => $request->title, 'color' => $request->color]);

        Session::flash('status', trans('ticket::project.name-has-been-modified',
            ['name' => $request->title]));

        \Cache::forget('ticket::projects');

        return redirect()->action('\mpba\Tickets\Controllers\ProjectsController@index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Response
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $title = $project->title;
        $project->delete();

        Session::flash('status', trans('ticket::projects.name-has-been-deleted', ['name' => $title]));

        \Cache::forget('ticket::projects');

        return redirect()->action('\mpba\Tickets\Controllers\ProjectsController@index');
    }
}
