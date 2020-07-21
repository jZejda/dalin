<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\DocBlock\Description;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);

        $user_posts = $user->posts->sortByDesc('updated_at')->take(10);
        $user_pages = $user->pages->sortByDesc('updated_at')->take(10);


        $last_posts = \DB::table('posts')
                            ->where('private', '=', 1)
                            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                            ->select('posts.*', 'posts.title', 'posts.created_at', 'posts.private', 'users.name', 'users.avatar', 'users.email', 'users.color')
                            ->orderBy('created_at', 'desc')
                            ->limit(2)
                            ->get();

        //dump($last_posts);

        return view('admin.dashboard.index', ['last_posts' => $last_posts, 'user_posts' => $user_posts, 'user_pages' => $user_pages]);
    }

    public function privatenews ()
    {

        $last_posts = \DB::table('posts')
            ->where('private', '=', 1)
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'posts.title', 'posts.created_at', 'posts.private', 'users.name', 'users.avatar', 'users.email', 'users.color')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'private_news' => $last_posts,
        ], 200);


    }
}
