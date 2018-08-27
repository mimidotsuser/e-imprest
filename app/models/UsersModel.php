<?php



use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;


class UsersModel
{

    /**
     * Add authentication record of a user
     * @param $screenId
     * @param $username
     * @param $password
     * @param $priv :int the privilege level of the user
     * @return mixed
     */
    public static function configAuth($screenId, $username, $password, $priv,$mobile)
    {
        return json_decode( Builder::table('userauth')
            ->insert($screenId,$username
                ,password_hash($password,PASSWORD_BCRYPT),
                $priv,Linter::sanitize(Carbon::now()),1,$mobile)
            ->into('screenId','username','password','privId','DateRecCreated','reset','mobile')
        ,true);

    }

    public static function authenticate()
    {

        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['userid'])){return Linter::jsonize($resp);};
        if (!isset($_POST['pass'])){return Linter::jsonize($resp);};


        //all clear
        $user=Linter::sanitize($_POST['userid']);
        $pass=Linter::sanitize($_POST['pass']);

        $resp=['status'=>'error','response'=>'Invalid password/username combination'];

        if (empty($user)||empty($pass)){return Linter::jsonize($resp); }

        //all set, lets now check if user has access to the system

        $ts=Builder::table('userauth')
            ->select('screenId','privId','password')
            ->where('username',$user)
            ->get();
        $ts=json_decode($ts,true);

        $ts['response']=$ts['response'][0]; //simplicity

        if($ts['status']=='error'){
            $resp['response']='server experiencing some problems. Please try again later';
            return Linter::jsonize($resp);
        }

        if($ts['code']==300){return Linter::jsonize($resp);} //returning null

        if(!password_verify($pass,$ts['response']['password'])){return Linter::jsonize($resp);} //no match

        //set session for current user
         Auth::setUserSess($ts['response']['screenId'],$ts['response']['privId']);
        //send the user a verification message

        if(env('APP_ENV')=='live') { //if application is live, let the send sms work
            $rs = Auth::sendOTP();
            if ($rs['status'] != 'success') {
                return $rs;
            }
            //load the code verification view
            header('Location:/login/two-step-verification-view');
            return 1;
        }else{
            Auth::accessControl();
        }
    }

    public static function getUserDetails($screenId)
    {

        return json_decode(
            Builder::table('staffdetails')
                ->where('screenId',Linter::sanitize($screenId))
                ->get()
            ,true);
    }

    /*
   * Change password of the current user
   *
   */
    public static function changePassword()
    {

        $out['status']='error';
        $out['response']='Unexpected error encountered. Please try again later';
        if (!isset($_POST['oldpass']) || !isset($_POST['newpass']) || !isset($_POST['confirmpass'])) {
          return Linter::jsonize($out);
        }

        $oldPass=Linter::sanitize($_POST['oldpass']);
        $pass1=Linter::sanitize($_POST['newpass']);
        $pass2=Linter::sanitize($_POST['confirmpass']);

        if(!hash_equals($pass1,$pass2)) {
            $out['response']='Both passwords do not match';
          return Linter::jsonize($out);
        }

        if(!self::checkOld($oldPass)) {
            $out['response']='Your current password does not match the records';
          return Linter::jsonize($out);
        }

        if(self::modifyPassword(Auth::getCurrentUserId(),$pass1)){
            $out['status']='success';
            $out['response']='Password changed successfully';
        }else{
            $out['response']='Unexpected error encountered. Please try again later';
        }
       return Linter::jsonize($out);
    }

    private static function checkOld($oldPass)
    {
        $rs=Builder::table('userauth')
            ->select('password')
            ->where('screenId',Auth::getCurrentUserId())
            ->get();

        $resp=json_decode($rs,true);

        if($resp['status']=='success' && $resp['response']!=null) {


            if(password_verify($oldPass,$resp['response'][0]['password'])){
                return true;
            }

        }
        return false;
    }

    private static function modifyPassword($memberId, $unencryptedPass)
    {
        $res=Builder::table('userauth')
            ->where('screenId',$memberId)
            ->update(['password'=>Linter::sanitize(password_hash($unencryptedPass,PASSWORD_BCRYPT))]);

        $resp=json_decode($res,true);

        if($resp['status']=='success' && $resp['response']=='success') {

            return true;
        }
        return false;
    }

}