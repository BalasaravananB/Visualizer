<?php
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'SiteAPILoginController@login');

Route::group(['middleware' => 'jwt.auth'], function() {
    
	

});

Route::post('/WheelByVehicle', 'SiteAPIController@WheelByVehicle'); 
Route::post('/getVehicles', 'SiteAPIController@getVehicles'); 
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::middleware('jwt.auth')->get('WheelByVehicle', function () {
//     return auth('api')->user();
// });


