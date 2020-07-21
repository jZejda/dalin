<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('/', function () {
    return view('welcome');
});

*/
/**
 * Frontend routes.
 */
Route::get('/', 'FrontEndController@index');
Route::get('/novinka/{id}', 'FrontEndController@novinka');
Route::get('novinky/vse', 'FrontEndController@postsAll');

Route::get('/stranka/{slug}', 'FrontEndController@stranka');

Route::prefix('cron')->group(function () {
    Route::get('/forecast/yoursecretkey/check', 'Cron\ServiceAppController@legForecast');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admin')->group(function () {
    Route::get('discord/newpost', 'PostController@sendToDiscord');

    Route::get('/dashboard', 'DashboardController@index');
    Route::get('/dashboard/json/privatenews', 'DashboardController@privatenews');

    Route::get('/post', 'PostController@index')->name('post');

    Route::resource('users', 'UserController');
    Route::resource('roles', 'RoleController');
    Route::resource('permissions', 'PermissionController');

    //Route::resource('members', 'MemberController');
    Route::get('members', 'MemberController@show')->name('members.show');
    Route::get('members/edit', 'MemberController@edit')->name('members.edit');
    Route::put('members/edit', 'MemberController@update')->name('members.update');
    Route::patch('members/edit', 'MemberController@update')->name('members.update');

    Route::get('members/edit-password', 'MemberController@editPassword')->name('members.editpassword');
    Route::put('members/update-password', 'MemberController@updatePassword')->name('members.updatepassword');
    Route::patch('members/update-password', 'MemberController@updatePassword')->name('members.updatepassword');

    Route::resource('posts', 'PostController');
    Route::resource('pages', 'PageController');

    Route::resource('oevents', 'OeventController');
    Route::get('oevent/{eventid}/{legid}', 'OeventController@event_leg_info');

    Route::get('oevents/list/{year}/{from}', function() {
        return view('admin.oevents.index');
    });
    Route::get('oevents/json/listallinyear/{year}/{from}', 'OeventController@get_oevent_data_api');


    Route::resource('legs', 'OeventLegController');
    Route::get('legs/create/{oeventid}', 'OeventLegController@create')->name('legs.create.first');

    Route::resource('contentcategories', 'ContentCategoryController');
    Route::get('manage-category', function () {
        return view('admin.category.index');
    });

    Route::get('contentcategories/showdetail/{id}', 'ContentCategoryController@showdetail');

    Route::get('media/{year}/files', 'MediamanagController@show');
    Route::post('media/store', 'MediamanagController@store')->name('admin.media.store');
    Route::get('media/{filename}/delete', 'MediamanagController@fileDelete');
});

//Devel routes

Route::prefix('devel')->group(function () {
    Route::get('/ui/components', function () {
        return view('devel.ui.components');
    });
    //Route::get('oevents/{year}/{from}', function() {
    //    return view('admin.oevents.index-devel');
    //});
    //Route::get('oevents/json/listallinyear/{year}/{from}', 'OeventController@index_devel');


});

//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
