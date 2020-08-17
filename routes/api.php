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

Route::group(array('middleware' => ['isVerifiedClient']), function ()
{

	Route::POST('/getVehicles', 'SiteAPIController@getVehicles'); 
	
	Route::POST('/findVehicle', 'SiteAPIController@findVehicle'); 
	
	Route::POST('/getLiftSizes', 'SiteAPIController@getLiftSizes'); 

	Route::POST('/getWheels', 'SiteAPIController@getWheels');  

	Route::POST('/WheelByVehicle', 'SiteAPIController@WheelByVehicle');  


	Route::POST('/setWheelVehicleFlow', 'SiteAPIController@setWheelVehicleFlow');  

});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::middleware('jwt.auth')->get('WheelByVehicle', function () {
//     return auth('api')->user();
// });


