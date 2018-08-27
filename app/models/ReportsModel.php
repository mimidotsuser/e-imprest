<?php

use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/8/2018
 * Time: 3:31 PM
 */

class ReportsModel
{

    /**
     * Total registered staff
     * @return mixed
     */
    public static function staffCount()
    {
        return json_decode(
            Builder::table('staff')
                ->select('count(screenId) as ct')
                ->get()
            ,true);

    }

    /**
     * Get total count of the active accounts for staff
     * @return mixed
     */
    public static function staffAccntActiveCount()
    {
        return json_decode(
            Builder::table('staff')
                ->select('count(screenId) as ct')
                ->where('active',1)
                ->get()
            ,true);

    }

    /**
     * Total number of departments
     * @return mixed
     */
    public static function departCount()
    {
        return json_decode(
            Builder::table('departments')
                ->select('count(departmentId) as ct')
                ->get()
            ,true);

    }

    public static function imprestWarrantApplicationsCount()
    {
        $user=AppGlobalsModel::currentUserDetails()['response'][0]['screenId'];
        return json_decode(
            Builder::table('requeststatusdetails')
            ->select('count(*) as ct')
            ->where('applicant',$user)
            ->get()
            ,true);
    }

    public static function imprestSurrenderApplicationsCount()
    {
        $user=AppGlobalsModel::currentUserDetails()['response'][0]['screenId'];
        return json_decode(
            Builder::table('surrenderstatusdetails')
                ->select('count(*) as ct')
                ->where('applicant',$user)
                ->get()
            ,true);
    }

    public static function warrantApprovalsCount()
    {
        $user=AppGlobalsModel::currentUserDetails()['response'][0]['screenId'];
        return json_decode(
            Builder::table('requeststatusdetails')
                ->select('count(*) as ct')
                ->where('designee',$user)
                ->get()
            ,true);
    }

    public static function surrenderApprovalsCount()
    {
        $user=AppGlobalsModel::currentUserDetails()['response'][0]['screenId'];
        return json_decode(
            Builder::table('surrenderstatusdetails')
                ->select('count(*) as ct')
                ->where('designee',$user)
                ->get()
            ,true);
    }
}