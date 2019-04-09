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
Route::prefix('users')->group(function () {
	
	// User listing page
	Route::get('/', 'UserController@index');

	// To fetch user list
	Route::get('/fetchuser', 'UserController@fetchUser');

	// To save user
	Route::post('/saveuser', 'UserController@saveUser');

	// To get the selected user details
	Route::get('/fetchuserdetails', 'UserController@fetchUserDetails');

	// To update user
	Route::post('/updateuser', 'UserController@updateUser');

	// To delete user
	Route::delete('/deleteuser', 'UserController@deleteUser');
});

// To fetch the images from storage and return it
Route::get('/images/{entity}/{filename}', function ($entity, $filename)
{
    // $path = storage_path() . '/uploads/' . $entity . '/' . $filename;
    $path = storage_path() . '/uploads/' . $filename;

    if(!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});