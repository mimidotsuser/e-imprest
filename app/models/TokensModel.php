<?php
/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/8/2018
 * Time: 12:53 PM
 */

class TokensModel
{

    /**
     * Generate a random and unique(nearly) 9 character string
     * @return mixed
     */
    public static function generateStaffId()
    {

        return str_split(
            md5(bin2hex(random_bytes(5))), //generate seeded random value, then encrypt using md5
            9)[1]; //return index 1
    }

    public static function generateSessionId()
    {
        return md5(base64_encode(bin2hex($_SESSION['uuid'])));
    }

    public static function genAccntCode()
    {
        return str_split(
            md5(bin2hex(random_bytes(5))), //generate seeded random value, then encrypt for more randomness
            7)[1]; //return index 1
    }

    public static function genTranscCode()
    {

        return strtoupper(str_split( md5(bin2hex(random_bytes(5))),7)[1]);
    }

    public static function genImprestRefCode()
    {
        return 'IMPR'.strtoupper(str_split( md5(bin2hex(random_bytes(5))),7)[1]);

    }

    public static function genOTP()
    {
        return strtoupper(str_split( md5(bin2hex(random_bytes(5))),6)[1]);
    }
}