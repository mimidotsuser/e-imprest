<?php
/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/21/2018
 * Time: 11:21 PM
 */

class ApiController
{

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
//        header("Content-Type:application/json");
    }

    public function departmentDetails()
    {
        Auth::middleware(['admin']); //only admin have access
        echo DepartmentsModel::fetchDetails();
    }

    public function staffDetails()
    {
        Auth::middleware(['admin']); //only admin have access

        echo StaffModel::fetchDetails();


    }

}