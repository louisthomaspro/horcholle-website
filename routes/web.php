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

Route::get('/', 'PageController@accueil');
Route::get('accueil', 'PageController@accueil')->name('accueil');
Route::get('activites', 'PageController@activites')->name('activites');
Route::get('presentation', 'PageController@presentation')->name('presentation');
Route::get('realisations', 'PageController@realisations')->name('realisations');
Route::get('realisations/{category_id}', 'PageController@category');
//Route::get('presse', 'PageController@presse')->name('presse');
Route::get('contact', 'PageController@contact')->name('contact');
Route::get('mentions-legales', 'PageController@mentions')->name('mentions');

Route::get('admin', 'AdminController@home');
Route::get('admin/home', 'AdminController@home')->name('admin.home');
Route::get('admin/syncPictures', 'AdminController@syncPictures')->name('admin.syncPictures');
Route::get('admin/syncRealisations', 'AdminController@syncRealisations')->name('admin.syncRealisations');

//Route::get('test', 'PageController@test')->name('test');

Route::put('api/text/update', 'AdminController@updateText')->name('admin.updatetext');


// Auth::routes();
// Authentication Routes...
$this->get('admin/login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('admin/login', 'Auth\LoginController@login');
$this->post('admin/logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
// $this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// $this->post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
