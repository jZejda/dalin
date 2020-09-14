<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('clearance')->except('show');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

        $id = Auth::user()->id;

        $user = User::findOrFail($id);

        $user_posts = $user->posts->sortByDesc('updated_at');

        return view('admin.members.show', ['user' => $user, 'user_posts' => $user_posts]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id); //Get user with specified id

        return view('admin.members.edit', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editPassword()
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id); //Get user with specified id

        return view('admin.members.editPassword', ['user' => $user]);
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id); //Get role specified by id

        //Validate name, email and password fields
        $this->validate($request, [
            'name'=>'required|max:120',
            'email'=>'required|email|unique:users,email,'.$id,
            //'password'=>'required|min:8|confirmed'
        ]);
        $input = $request->only(['name', 'email', 'color']); //Retreive the name, email and password fields
        $user->fill($input)->save();

        return redirect()->route('members.show')
            ->with('flash_message',
                'Upravil jsi uživatelské nastavení.');
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id); //Get role specified by id

        //Validate name, email and password fields
        $this->validate($request, [
            'password'=>'required|min:8|confirmed',
        ]);


        $request->user()->fill([
            'password' => Hash::make($request->password)
        ])->save();

        //$input = $request->only(['password']); //Retreive the password fields
        //$password = Hash::make('secret123');

        //$user->fill($password)->save();

        return redirect()->route('members.show')
            ->with('flash_message',
                'Nastavil jsi nové heslo');
    }
}
