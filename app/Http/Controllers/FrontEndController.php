<?php

namespace App\Http\Controllers;

use DB;
use Storage;

use App\Page;
use App\Post;
use App\Oevent;
use App\Region;
use App\Discipline;
use App\Oevent_results;

use App\Content_category;

use App\Http\Controllers\Tool\EventResultController;
use Illuminate\Support\Facades\App;

class FrontEndController extends Controller
{
    public function index()
    {
        $posts = DB::table('posts')
            ->whereNull('private')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name AS user_name', 'users.color AS user_color')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        //dump($posts);

        // Events part

        $disciplines = Discipline::get();
        $disciplines = \Illuminate\Support\Arr::pluck($disciplines, 'long_name', 'id');

        $regions = Region::get();
        $regions = \Illuminate\Support\Arr::pluck($regions, 'short_name', 'id');


        $today = date('Y-m-d');
        $oevents = Oevent::where('to_date', '>=', $today)
            ->orWhere('from_date', '>=', $today)
            ->orderBy('from_date', 'ASC')
            ->with(array('legs' => function ($query) {
                $query->orderBy('leg_datetime', 'ASC');
            }))->limit(4)->get();


        //dump($oevents->toArray());
        //dd($today);

        return view('frontend.index', ['posts' => $posts, 'oevents' => $oevents, 'regions' => $regions, 'disciplines' => $disciplines]);
    }

    public function novinka($id = null)
    {
        $post = Post::findOrFail($id);

        return view('frontend.post', ['post' => $post]);
    }

    public function postsAll()
    {
        $posts = DB::table('posts')
            ->whereNull('private')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name AS user_name', 'users.color AS user_color')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('frontend.postsAll', ['posts' => $posts]);
    }

    /**
     * stranka - show in frontend.
     * @param string $slug -nice URL adress
     * @return Response
     */
    public function stranka($slug)
    {
        //$page = Page::findOrFail($id);
        //TODO select only open pages, others return 404
        $page = Page::where('slug', '=', $slug)
            ->where('status', 'open')
            ->first();
        if (!$page) {
            abort(404);
        }

        // if page have set the menu content
        if ($page->page_menu == 1) {
            $pagesMenu = Page::where('content_category_id', '=', $page->content_category_id)
                ->where('status', 'open')
                ->get();
            $categoryName = Content_category::findOrFail($page->content_category_id);
        } else {
            $pagesMenu = null;
            $categoryName = null;
        }

        //  dd($categoryName);

        return view('frontend.page', compact('page', 'pagesMenu', 'categoryName'));
    }


    public function oeventResult($id)
    {
        $result = Oevent_results::where('id', '=', $id)->first();

        // Event data
        if (isset($result['oevent_id'])) {
            $oevent_data = Oevent::where('id', '=', $result['oevent_id'])->first();
            if (isset($oevent_data['url'])) {
                $oevent_url = $oevent_data['url'];
            } else {
                $oevent_url = null;
            }
        } else {
            $oevent_url = null;
        }


        if (config('app-config.result_type') !== null) {
            $result_type = config('app-config.result_type');
        } else {
            $result_type = null;
        }

        if (!empty($result) && $result['result_type'] == $result_type[1]) {

            $file_exist = Storage::disk('eventdata')->exists($result['result_path']);

            $show_result = array(
                'result_type' => $result_type[1],
                'result_id' => $result->id,
                'resource_exist' => $file_exist
            );
        } elseif (!empty($result) && $result['result_type'] == $result_type[2]) {

            // TODO test na resoure html odkazu

            $show_result = array(
                'result_type' => $result_type[2],
                'result_id' => $result->id,
                'resource_exist' => true
            );
        } else {
            $show_result = null;
        }


        return view('frontend.event-result', ['show_result' => $show_result, 'oevent_url' => $oevent_url]);
    }
}
