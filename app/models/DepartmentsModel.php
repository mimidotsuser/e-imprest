<?php

use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/8/2018
 * Time: 10:53 AM
 */

class DepartmentsModel
{
    public static function userRoles()
    {
        return json_decode(
            Builder::table('userroles')
            ->select('roleId as id','name')
            ->get(),
            true
        );
    }
    public static function fetchAll()
    {

        return json_decode(
            Builder::table('departments')
                ->select('departmentId as id,name,parentDepartment as parent')
            ->get(),
            true
        );
    }

    public static function add()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['parent']) || empty(Linter::sanitize($_POST['parent']))) {
            $resp['response'] = "Parent department has not been provided";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['dept'])) {
            $resp['response'] = "The name of department to be added has not been provided";
            return Linter::jsonize($resp);
        }
       //everything else is set

        $rs=Builder::table('departments')
            ->insert(
                Linter::sanitize($_POST['parent']),
                Linter::sanitize($_POST['dept']),
                Linter::sanitize(Carbon::now())
            )
            ->into('parentDepartment','name','dateRecCreated');

        $rs=json_decode($rs,true);
        if($rs['status']='success'){
            $resp = ['status' => 'success', 'response' => "Department added successfully"];
            return Linter::jsonize($resp);
        }
        $resp['response'] = "Department could not be added. Please try again or contact the webmaster";
        return Linter::jsonize($resp);
    }

    public static function fetchDetails()
    {

        return Builder::table('departments')
            ->where('departmentId',Linter::sanitize($_POST['id']))
            ->get();
    }

    public static function update()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['parent']) || empty(Linter::sanitize($_POST['parent']))) {
            $resp['response'] = "Parent department has not been provided";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['dept'])) {
            $resp['response'] = "The name of department to be added has not been provided";
            return Linter::jsonize($resp);
        }
        //everything else is set


        $rs=Builder::table('departments')
            ->where('departmentId',Linter::sanitize($_POST['id']))
            ->update(['name'=>Linter::sanitize($_POST['dept']),'parentDepartment'=>$_POST['parent']]);
        $rs=json_decode($rs,true);
        if($rs['status']='success'){
            $resp = ['status' => 'success', 'response' => "Department added successfully"];
            return Linter::jsonize($resp);
        }
        $resp['response'] = "Department could not be added. Please try again or contact the webmaster";
        return Linter::jsonize($resp);
    }

}