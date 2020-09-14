<?php

namespace App\Http\Controllers;

use Session;
use App\Page;
use App\Content_category;
use Illuminate\Http\Request;

class ContentCategoryController extends Controller
{
    /**
     * Only for authenticated users.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ccats = Content_category::orderBy('title', 'asc')->get();

        return response()->json([
            'ccats' => $ccats,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',

        ]);

        $ccat = Content_category::create([
            'title' => request('title'),
            'description' => request('description'),
        ]);

        return response()->json([
            'ccat' => $ccat,
            'message' => 'Success',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Content_category $content_category
     * @return \Illuminate\Http\Response
     */
    public function show(Content_category $content_category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Content_category $content_category
     * @return \Illuminate\Http\Response
     */
    public function edit(Content_category $content_category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Content_category $content_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content_category $content_category)
    {
        $ccat = Content_category::findOrFail(request('id'));

        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'slug' => 'required|unique:content_categories,slug,\'.$content_category->id',
        ]);

        //$content_category->title = request('title');
        //$content_category->description = request('description');
        //$content_category->save();

        $input = $request->all();
        $ccat->fill($input)->save();

        //$content_category->update($request->all());

        return response()->json([
            'message' => 'Kategorie upravena!',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Content_category $content_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content_category $content_category)
    {
        //
    }

    /**
     * Show detail.
     *
     * @param  $id  integer
     * @return \Illuminate\Http\Response
     */
    public function showdetail($id)
    {
        $ccatDetail = Content_category::findOrFail($id);

        return response()->json([
            'ccatDetail' => $ccatDetail,
        ], 200);
    }
}
