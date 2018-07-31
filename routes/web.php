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

Route::prefix('/api/v1')->group(function () {
    Route::get('search', 'ListingsSearchController@index')->name('listings.search');
    Route::get('listings', 'FeaturedListingsController@index')->name('listings.featured');
    Route::get('agent-listings/{agent}', 'AgentListingsController@index')->name('agent.listings');
});
