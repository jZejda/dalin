<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use Config;
use Session;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

// TODO add correct URL to default image
define('DEFFAULT_IMG_URL', 'images/app-default/news-default-bg.jpg');

class PostController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'clearance'])->except('index', 'show');
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
        //$posts = Post::orderby('id', 'desc')->paginate(5); //show only 5 items at a time in descending order
        $posts = \DB::table('posts')
                  ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                  ->select('posts.*', 'posts.title', 'posts.created_at', 'posts.private', 'users.name', 'users.color')
                  ->orderBy('created_at', 'desc')
                  ->get();

        //dump($posts);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /*
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validating title and body field
        $this->validate($request, [
            'title'=>'required|max:100',
            'editorial'=> 'required|max:255',
            ]);

        $postTitle = $request['title'];
        $postEditorial = $request['editorial'];

        $input = $request->all();

        if ($request['private'] == 'on') {
            $input['private'] = 1;
        }

        if ($request['img_url'] == null) {
            $input['img_url'] = DEFFAULT_IMG_URL;
        }
        Post::create($input);

        Session::flash('flash_message', 'Právě jsi vytvořil novinku: '.$postTitle);

        Log::info('Vytvořena novinka: '.$postTitle);

        // Discord notification for correctures
        // like https://gist.github.com/Mo45/cb0813cb8a6ebcd6524f6a36d4f8862c
        if (Config::get('discordhooks.discord_meta.status')) {
            $url = 'https://discordapp.com/api/webhooks/'.Config::get('discordhooks.discord_hooks_url.chanel1_url');
            $hookObject = json_encode([
                'username' => Config::get('discordhooks.discord_meta.bot_name'), //username
                'content' => 'Na stránkách byla vytvořena novinka, prosím o případnou kontrolu.',
                'tts' => false, //Whether or not to read the message in Text-to-speech

                'embeds' => [ // First Embed
                    [
                        'title' => 'Novinka: '.$postTitle,
                        'description' => 'Úvodník: '.$postEditorial,
                        //"url" => "https://www.google.com/", // The URL of where your title will be a link to
                        'timestamp' => date('c'), //This must be formatted as ISO8601
                        // Footer object
                        'footer' => [
                            'text' => 'oPlan',
                        ],
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $hookObject,
                CURLOPT_HTTPHEADER => [
                    'Length' => strlen($hookObject),
                    'Content-Type' => 'application/json',
                ],
            ]);

            $response = curl_exec($ch);
            curl_close($ch);
        }

        //Display a successful message upon save
        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        $post_user = Post::findOrFail($id)->user; //Find post of id = $id
        //dump($post_user);
        return view('admin.posts.show', ['post' => $post, 'post_user' => $post_user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        return view('admin.posts.edit', compact('post'));
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'=>'required|max:100',
            'editorial'=> 'required|max:255',
            //'img_url'=> 'required'
        ]);

        $post = Post::findOrFail($id);
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->private = $request->input('private');
        $post->editorial = $request->input('editorial');

        if ($post->private == 'on') {
            $post->private = 1;
        } else {
            $post->private = null;
        }

        if ($request->input('img_url') == null) {
            $post->img_url = DEFFAULT_IMG_URL;
        } else {
            $post->img_url = $request->input('img_url');
        }

        $post->save();

        return redirect()
            ->route('posts.show', $post->id)
            ->with('flash_message', 'Právě jsi aktualizoval novinku: '.$post->title);
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')
            ->with('flash_message', 'Obsah novinky byl smazán');
    }

    public function sendToDiscord()
    {
        $url = 'https://discordapp.com/api/webhooks/'.Config::get('discordhooks.discord_hooks_url.chanel1_url');
        $hookObject = json_encode([
            'username' => Config::get('discordhooks.discord_meta.bot_name'), //username
            'content' => 'Na stránkách byla vytvořena novinka, prosím o případnou kontrolu.',
            'tts' => false, //Whether or not to read the message in Text-to-speech

            'embeds' => [ // First Embed
                [
                    'title' => 'Novinka',
                    'description' => 'Prvních xy řádků novinky by to chtělo asi :-)',
                    'url' => 'https://www.google.com/', // The URL of where your title will be a link to
                    'timestamp' => '2018-03-10T19:15:45-05:00', //This must be formatted as ISO8601
                    // Footer object
                    'footer' => [
                        'text' => 'oPlan',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $hookObject,
            CURLOPT_HTTPHEADER => [
                'Length' => strlen($hookObject),
                'Content-Type' => 'application/json',
            ],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
    }
}
