<?php
/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 3/2/2018
 * Time: 8:59 PM
 */
namespace Model;

class Linter
{
    public static function sanitize($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function isNull($val)
    {
       if(!isset($val) || empty($val)){
           return true;
       }
       return false;
    }

    /**
     * Transform an array to json formatted string
     * @param $data :array
     * @return mixed
     */
    public static function jsonize($data)
    {
        return json_decode(json_encode($data),true);
    }

}