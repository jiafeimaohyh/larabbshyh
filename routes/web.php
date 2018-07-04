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
Route::get('/', 'PagesController@root')->name('root');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


//处理 edit 页面提交的更改
Route::put('/users/{user}', 'UsersController@update')->name('users.update');


//显示用户个人信息页面

Route::get('/users/{user}', 'UsersController@show')->name('users.show');

//显示编辑个人资料页面
Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');



Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);

Route::get('topics/{topic}/{slug?}', 'TopicsController@show')->name('topics.show');


Route::resource('categories', 'CategoriesController', ['only' => ['show']]);

//上传图片
Route::post('upload_image', 'TopicsController@uploadImage')->name('topics.upload_image');


Route::resource('replies', 'RepliesController', ['only' => ['store', 'destroy']]);


//消息通知显示
Route::resource('notifications', 'NotificationsController', ['only' => ['index']]);


// 访问后台 无权限时 重定向到该地址
Route::get('permission-denied', 'PagesController@permissionDenied')->name('permission-denied');