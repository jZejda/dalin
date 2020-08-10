<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Page;
use App\User;
use App\Content_category;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('clearance')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$pages = Page::orderby('id', 'desc')->paginate(25);

        $pages = \DB::table('pages')
                  ->leftJoin('users', 'pages.user_id', '=', 'users.id')
                  ->select('pages.*', 'users.name', 'users.color')
                  ->orderBy('created_at', 'desc')
                  ->get();

        $category = Content_category::orderBy('id')->pluck('title', 'id');

        return view('admin.pages.index', compact('pages', 'category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Content_category::orderBy('id')->pluck('title', 'id');
        //TODO - tady selectuj jen kdo má práva Redaktora
        $users_editor = User::orderBy('name')->pluck('name', 'id');

        return view('admin.pages.create', compact('category', 'users_editor'));
    }

    /*
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
        ]);

        $input = $request->all();

        Page::create($input);
        Session::flash('flash_message', 'Vytvořil jsi novou stránku.'.$request->title);

        return redirect()->route('pages.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {

        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        //dump($page);
        $category = Content_category::orderBy('id')->pluck('title', 'id');
        $users_editor = User::orderBy('name')->pluck('name', 'id');

        return view('admin.pages.edit', compact('page', 'category', 'users_editor'));
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'slug' => 'required|unique:pages,slug,'.$page->id,
        ]);

        $input = $request->all();
        $page->fill($input)->save();

        if (! array_key_exists('page_menu', $input)) {
            Page::where('id', $page->id)->update(['page_menu' => 0]);
        }

        //Session::flash('flash_message', 'Upravil jsi stranku.');

        return redirect()
            ->route('pages.show', $page->id)
            ->with('flash_message', 'Právě jsi aktualizoval stránku: '.$page->title);
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('pages.index')
            ->with('flash_message', 'Obsah stánky byl smazán');
    }
}
