<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;

use App\UserDetail;

class UserController extends Controller
{
	/**
     * Function to return user listing view
     * @param void
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	return view('users');
    }

    /**
     * Function to fetch user list
     * @param void
     * @return \Illuminate\Http\Response
     */
    public function fetchUser()
    {
    	$query = UserDetail::query();

    	$query->when(Input::get('searchText') != '', function ($q) {
    	    return $q->where('name', 'like', '%' . Input::get('searchText') . '%');
    	});

    	$users = $query->get();

    	return response()->json($users);
    }

    /**
     * Function to save user
     * @param void
     * @return \Illuminate\Http\Response
     */
    public function saveUser()
    {
    	$userId = Input::get('userId');
    	$name 	= Input::get('name');
    	$email 	= Input::get('email');
    	$phone 	= Input::get('phone');
    	$image 	= Input::file('image');

        // Server Side Validation
        $response =array();

		$validation = Validator::make(
		    array(
		        'name'	=> $name,
		        'email'	=> $email,
		        'phone'	=> $phone
		    ),
		    array(
		        'name' 	=> array('required'),
		        'email' => array('required', 'email'),
		        'phone'	=> array('required')
		    ),
		    array(
		        'name.required'	=> 'Please enter name.',
		        'email.required'=> 'Please enter email.',
		        'email.email'	=> 'Please enter a valid email.',
		        'phone.required'=> 'Please enter phone no.'
		    )
		);

		if ( $validation->fails() )		// Some data is not valid as per the defined rules
		{
			$error = $validation->errors()->first();

		    if( isset( $error ) && !empty( $error ) )
		    {
		        $response['errCode']    = 1;
		        $response['errMsg']     = $error;
		    }
		}
		else 							// data is valid save it
		{
			if( $userId == '' )
			{
				// If file is available upload it
				if( !is_null( $image ) && $image->isValid() )
				{
				    $fileExt  = $image->getClientOriginalExtension();
				    $fileType = $image->getMimeType();
				    $fileSize = $image->getSize();

				    if( ( $fileType == 'image/jpeg' || $fileType == 'image/jpg' || $fileType == 'image/png' ) && $fileSize <= 1000000 )
				    {
				        // Rename the file
				        $fileNewName = strtolower(str_random(40)) . '.' . $fileExt;
				        $destinationPath = storage_path() . '/uploads';

				        $image->move( $destinationPath, $fileNewName );

				        $userDetail = new UserDetail;

				        $userDetail->name 		= $name;
				        $userDetail->email 		= $email;
				        $userDetail->phone 		= $phone;
				        $userDetail->profile_pic= $fileNewName;
				        $userDetail->created_at = date('Y-m-d H:i:s');

				        if( $userDetail->save() )
				        {
	        		        $response['errCode']    = 0;
	        		        $response['errMsg']     = 'Details saved successfully.';
	        		        $response['userDetail'] = $userDetail;
	        		    }
	        		    else
	        		    {
	        		    	$response['errCode']    = 2;
	        		        $response['errMsg']     = 'Some error is saving user details.';
	        		    }
				    }
				    else
	    		    {
	    		    	$response['errCode']    = 4;
	    		        $response['errMsg']     = 'Only image file allowed.';
	    		    }
				}
				else
				{
			        $userDetail = new UserDetail;

			        $userDetail->name 		= $name;
			        $userDetail->email 		= $email;
			        $userDetail->phone 		= $phone;
			        $userDetail->created_at = date('Y-m-d H:i:s');

			        if( $userDetail->save() )
			        {
	    		        $response['errCode']    = 0;
	    		        $response['errMsg']     = 'Details saved successfully.';
	    		        $response['userDetail'] = $userDetail;
	    		    }
	    		    else
	    		    {
	    		    	$response['errCode']    = 4;
	    		        $response['errMsg']     = 'Some error is saving user details.';
	    		    }
				}
			}
			else
			{
				// If file is available upload it
				if( !is_null( $image ) && $image->isValid() )
				{
				    $fileExt  = $image->getClientOriginalExtension();
				    $fileType = $image->getMimeType();
				    $fileSize = $image->getSize();

				    if( ( $fileType == 'image/jpeg' || $fileType == 'image/jpg' || $fileType == 'image/png' ) && $fileSize <= 1000000 )
				    {
				        // Rename the file
				        $fileNewName = strtolower(str_random(40)) . '.' . $fileExt;
				        $destinationPath = storage_path() . '/uploads';

				        $image->move( $destinationPath, $fileNewName );

				        $userDetail = UserDetail::find($userId);

				        $userDetail->name 		= $name;
				        $userDetail->email 		= $email;
				        $userDetail->phone 		= $phone;
				        $userDetail->profile_pic= $fileNewName;
				        $userDetail->created_at = date('Y-m-d H:i:s');

				        if( $userDetail->save() )
				        {
	        		        $response['errCode']    = 0;
	        		        $response['errMsg']     = 'Details updated successfully.';
	        		        $response['userDetail'] = $userDetail;
	        		    }
	        		    else
	        		    {
	        		    	$response['errCode']    = 2;
	        		        $response['errMsg']     = 'Some error is updating user details.';
	        		    }
				    }
				    else
	    		    {
	    		    	$response['errCode']    = 4;
	    		        $response['errMsg']     = 'Only image file allowed.';
	    		    }
				}
				else
				{
			        $userDetail = UserDetail::find($userId);

			        $userDetail->name 		= $name;
			        $userDetail->email 		= $email;
			        $userDetail->phone 		= $phone;
			        $userDetail->created_at = date('Y-m-d H:i:s');

			        if( $userDetail->save() )
			        {
	    		        $response['errCode']    = 0;
	    		        $response['errMsg']     = 'Details updated successfully.';
	    		        $response['userDetail'] = $userDetail;
	    		    }
	    		    else
	    		    {
	    		    	$response['errCode']    = 4;
	    		        $response['errMsg']     = 'Some error is updating user details.';
	    		    }
				}
			}
		}

		return response()->json($response);
    }

    /**
     * Function to delete user
     * @param void
     * @return \Illuminate\Http\Response
     */
    public function deleteUser()
    {
    	$userId = Input::get('userId');

    	UserDetail::where(['id' => $userId])->delete();
    }

    /**
     * Function to get the selected user details
     * @param void
     * @return \Illuminate\Http\Response
     */
    public function fetchUserDetails()
    {
    	$userId = Input::get('userId');

    	$userDetails = UserDetail::where(['id' => $userId])->first();

    	return response()->json($userDetails);
    }
}