<?php

use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/5/2018
 * Time: 6:33 PM
 */
class AppGlobalsModel{

    public static function baseurl()
    {
        return env('APP_URL');
    }

    public static function appname()
    {
        return "Imprest Manager";
    }

    public static function author()
    {
        return "holla@mimidots.com";
    }

    public static function currentUserDetails()
    {

        return json_decode(
            Builder::table('staffdetails')
            ->where('screenId',Auth::getCurrentUserId())
            ->get()
            ,true);
    }
}