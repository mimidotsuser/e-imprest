<?php

/**
 * All rights reserved.
 * User: Dread Pirate Roberts
 * Date: 06-Aug-17
 * Time: 16:50
 */




class HomeController {
	
	public static function index(){

        Auth::revokeAccess(); //if user ia already logged in, revoke their access
        view('login');
	}


    public function authenticate()
    {
        $data=['notify'=>UsersModel::authenticate()];
        view('login',$data);

    }

    public static function twowayauthview()
    {
        view('smsauth',['mobile'=>Auth::getMaskedUserMobile()]);
    }
    public function twowayauth()
    {
        $data=[
            "notify"=>Auth::OTPVerify(),
            'mobile'=>Auth::getMaskedUserMobile()
        ];
        view('smsauth',$data);

    }
}