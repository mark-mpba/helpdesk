<?php

namespace mpba\Tickets\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use mpba\Tickets\Helpers\LaravelVersion;
use mpba\Tickets\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // seconds expected for L5.8<=, minutes before that
        $time = LaravelVersion::min('5.8') ? 60 * 60 : 60;
        $categories = \Cache::remember('ticket::categories', $time, function () {
            return Category::all();
        });

        return view('ticket::admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('ticket::admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'color' => 'required',
        ]);

        $category = new Category();
        $category->create(['name' => $request->name, 'color' => $request->color]);

        Session::flash('status', trans('ticket::lang.category-name-has-been-created', ['name' => $request->name]));

        \Cache::forget('ticket::categories');

        return redirect()->action('\mpba\Tickets\Controllers\CategoriesController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return 'All category related agents here';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('ticket::admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'color' => 'required',
        ]);

        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name, 'color' => $request->color]);

        Session::flash('status', trans('ticket::lang.category-name-has-been-modified', ['name' => $request->name]));

        \Cache::forget('ticket::categories');

        return redirect()->action('\mpba\Tickets\Controllers\CategoriesController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        Session::flash('status', trans('ticket::lang.category-name-has-been-deleted', ['name' => $name]));

        \Cache::forget('ticket::categories');

        return redirect()->action('\mpba\Tickets\Controllers\CategoriesController@index');
    }
}
