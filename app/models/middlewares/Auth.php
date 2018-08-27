<?php

use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;

if (session_status() == PHP_SESSION_NONE) {
    session_name('mimidots');
    session_start();
}
class Auth {

    private static $userId = 'uuid'; //current user screen id
    private static $admnUId = 'assid'; //admin session name
    private static $stsUId = 'stssid';
    private static $roleId='r'; //staff session name

    /**
     * @param $screenId :string the user screen id
     * @return string
     */
    public static function accessControl()
    {
        $privId=$_SESSION[static::$roleId];
        $screenId=$_SESSION[static::$userId];
        unset($_SESSION[static::$roleId]);//unset the role id session{security purpose}

        if($privId==1){ //admin

            header("Location:/admin");
            self::setAdminSess($screenId);

            return '';
        }elseif ($privId==2){ //staff
            header("Location:/staff");
            self::setStaffSess($screenId);
            return '';
        }else{
            //redirect back
            header("Location:/login");
            return '';
        }
    }

    public static function setUserSess($screenId,$roleId)
    {
        $_SESSION[static::$userId] = $screenId;
        $_SESSION[static::$roleId]=$roleId;
    }

    /**
     * Add staff session
     * @param $screenId
     */
    private static function setStaffSess($screenId)
    {

        $_SESSION[static::$stsUId] = TokensModel::generateSessionId();

    }

    /**
     * Add admin session
     * @param $screenId
     */
    private static function setAdminSess($screenId)
    {
        $_SESSION[static::$admnUId] = TokensModel::generateSessionId();

    }


    /**
     *Unset all the session variables
     */
    public static function revokeAccess()
    {
        session_unset();
    }


    /**
     * Checks if user has the access to resources or to perform an action
     * @param array $users
     */
    public static function middleware(array $users)
    {

        //check first if no session has been set for current user
        if(!isset($_SESSION[self::$userId]) || empty($_SESSION[self::$userId])){
            header('Location:/login');
            return;
        }


        //when less than one user is provided
        if (count($users)<=1){
            if($users[0]=='admin'){
                if(!isset($_SESSION[self::$admnUId]) || empty($_SESSION[self::$admnUId])){
                    header('Location:/login');
                    return;
                }
            }elseif ($users[0]=='staff'){
                if(!isset($_SESSION[self::$stsUId]) || empty($_SESSION[self::$stsUId])){
                    header('Location:/login');
                    return;
                }
            }
        }
    }

    /**
     * Returns the current user screen id
     * @return mixed
     */
    public static function getCurrentUserId()
    {

        return isset($_SESSION[self::$userId])?$_SESSION[self::$userId]:" ";
    }

    public static function sendOTP()
    {
        $code=TokensModel::genOTP();
        //store the code in the database {should expire after 5 minutes}
        $rsp=json_decode(
            Builder::table('userauth')
            ->where('screenId',$_SESSION[static::$userId])
            ->update(['otp'=>$code,'otpExpire'=>Carbon::now()->addMinute(5)])
            ,true);

        if($rsp['status']=='success'){
            $user=UsersModel::getUserDetails($_SESSION[static::$userId])['response'][0];
            //send the sms
            if(env('APP_ENV')=='live') {
                SMSModel::sendSMS(
                    "Your one time password is: " . $code,
                    $user['mobile']
                );
            }

            return ['status'=>'success','response'=>$user['mobile']];
        }

        return ['status'=>'error','response'=>'Error with two way authentication service. Please try again later'];

    }

    public static function getMaskedUserMobile()
    {
        return substr_replace(UsersModel::getUserDetails($_SESSION[static::$userId])['response'][0]['mobile'],
            '***',7,4);
    }

    public static function OTPVerify()
    {

        $resp = ['status' => 'error', 'response' => "Invalid verification code. Please check and try again"];

        $rs=Builder::table('userauth')
            ->select('otpExpire')
            ->where('screenId',$_SESSION[static::$userId])
            ->andWhere('otp',Linter::sanitize($_POST['vcode']))
            ->get();
        $rs=json_decode($rs,true);

        if($rs['code']==300){return $resp;}

        //check if it is expired
        if(Carbon::now()->diffInMinutes($rs['response'][0]['otpExpire'])<1){
            $resp['response']='Your code has already expired. Click the link to generate new code';
            return $resp;
        }
        //has access
        Auth::accessControl();
        return 1;
    }

}