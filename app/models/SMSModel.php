<?php

use AfricasTalking\SDK\AfricasTalking;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/25/2018
 * Time: 3:41 AM
 */

class SMSModel
{

    public static function sendSMS($message, $recipient)
    {
        $AT=new AfricasTalking(env('AFRICASTALKING_USERNAME'),env('AFRICASTALKING_KEY'));

        $smsObj=$AT->sms();

        $smsObj->send(['to'=>$recipient,'message'=>$message]);

    }

}