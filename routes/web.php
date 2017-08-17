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
    // Route::get('authors', 'AuthorsController@index');
    // Route::get('authors/create', 'AuthorsController@create');
    // Route::post('authors', 'AuthorsController@store');

    // Route::get('authors/{id}', 'AuthorsController@show');

    // Route::get('authors/{id}/edit', 'AuthorsController@edit');    
    // Route::put('authors/{id}', 'AuthorsController@update');
    
    // Route::delete('authors/{id}', 'AuthorsController@destroy');

Route::get('/','GuestController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['prefix' => 'admin', 'middleware' => ['auth','role:admin']], function (){
    Route::resource('authors', 'AuthorsController');
    Route::resource('books', 'BooksController');
    Route::resource('members', 'MembersController');
});

Route::get('books/{book}/borrow', [
    'middleware' => ['auth','role:member'],
    'as'         => 'guest.books.borrow',
    'uses'       => 'BooksController@borrow'
]);

Route::put('books/{book}/return', [
    'middleware' => ['auth', 'role:member'],
    'as'         => 'member.books.return',
    'uses'       => 'BooksController@returnBack'
]);

//Route verify
Route::get('auth/verify/{token}', 'Auth\RegisterController@verify');
Route::get('auth/send-verification', 'Auth\RegisterController@sendVerification');

//Route Profile
Route::get('settings/profile', 'SettingsController@profile');
Route::get('settings/profile/edit', 'SettingsController@editProfile');
Route::post('settings/profile', 'SettingsController@updateProfile');

//Route ganti password
Route::get('settings/password', 'SettingsController@editPassword');
Route::post('settings/password', 'SettingsController@updatePassword');

