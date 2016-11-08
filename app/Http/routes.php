<?php

use Illuminate\Support\Facades\Redirect;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/index', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/', function () {
    //return view('welcome');
    return Redirect::to('import');
});

Route::get('import', function(){
    return view('import');
});

Route::post('import_csv','ImportazioneCsvController@store');

Route::post('import_csv2','ImportazioneCsvController@store2');

Route::post('import_csv3','ImportazioneCsvController@store3');

Route::get('show','ImportazioneCsvController@show');

Route::get('showone/{id}', 'ImportazioneCsvController@showOne');

Route::get('dropTable/{tablename}','ImportazioneCsvController@dropTable');

Route::post('downloadExcel/{type}', 'ImportazioneCsvController@downloadExcel');

Route::get('readlocalfile/{filename}','ImportazioneCsvController@readLocalFile');

