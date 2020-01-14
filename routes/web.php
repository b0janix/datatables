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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/admin/user', 'Admin\UserController@index');
Route::resource('/datatable/user','Datatable\UserController');
Route::post('/datatable/user/search','Datatable\UserController@searchByColumn')->name('searchByColumn');


