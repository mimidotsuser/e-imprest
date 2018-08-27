<?php

use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/9/2018
 * Time: 11:32 AM
 */

class FinanceModel
{
    public static function getFinancialYears()
    {
        return json_decode(
            Builder::table('financialyears')
            ->get()
            ,true);
    }

    public static function manage()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_GET['action']) || empty($_GET['action'])){return Linter::jsonize($resp);}

        if ($_GET['action']=='add'){
            return self::addFinYear();
        }

        if ($_GET['action']=='activate'){
            return self::deactivate();
        }
    }

    public static function addFinYear()
    {

        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['startdate']) || empty(Linter::sanitize($_POST['startdate']))) {
            $resp['response'] = "Start date has not been provided. Please try again";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['enddate']) || empty(Linter::sanitize($_POST['enddate']))) {
            $resp['response'] = "End date has not been provided. Please try again";
            return Linter::jsonize($resp);
        }

        //all set
        $starton=Linter::sanitize($_POST['startdate']);
        $endon=Linter::sanitize($_POST['enddate']);
        $yr=explode('-',$starton)[0];

        $finyear= Carbon::parse($starton);
        $st=$finyear->toArray()['year'];
        $end=$finyear->addYear(1)->toArray()['year'];


        $re= json_decode(
            Builder::table('financialyears')
            ->insert($st.'-'.$end,$starton,$endon,Linter::sanitize(Carbon::now()))
            ->into('financialYear','startdate','enddate','dateRecCreated')
            ,true
        );
        if($re['status']=='success'){
            $resp['status']='success';
            $resp['response']='Financial year added successfully';
            return Linter::jsonize($resp);
        }
        $resp['response']='Error occurred. Please contact the webmaster'.$re['response'];
        return Linter::jsonize($resp);
    }

    public static function deactivate()
    {
        $resp = ['status' => 'error', 'response' => "Your request appear broken. Please try again."];

        if (!isset($_POST['yearId']) || empty($_POST['yearId'])){return Linter::jsonize($resp);}

        //init the ledger
        $ser=LedgerModel::initStartYear(Linter::sanitize($_POST['finYear']));
        if(!empty($ser)){
          $resp['response']="Error occurred when initializing the ledger { $ser }";
          return Linter::jsonize($resp);
        }

        //else, lets then set all other deactive, then activate
        Builder::table('financialyears')
            ->update(['status'=>0]);
        $rp=Builder::table('financialyears')
            ->where('id',Linter::sanitize($_POST['yearId']))
            ->update(['status'=>1]);
        $rp=json_decode($rp,true);
        if($rp['status']=='success'){
            $resp['status']='success';
            $resp['response']='Update successful';
            return Linter::jsonize($resp);
        }
        $resp['response']='Error occurred. Please contact the webmaster'.$rp['response'];
        return Linter::jsonize($resp);
    }

    public static function budgetEntry()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_GET['action']) || empty($_GET['action'])){return Linter::jsonize($resp);}

        if ($_GET['action']=='add'){
            return self::addBudgetEntry();
        }

    }

    private static function addBudgetEntry()
    {

        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['finYear']) || empty(Linter::sanitize($_POST['finYear']))) {
            $resp['response'] = "Financial year has not been provided. Please try again";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['dept']) || empty(Linter::sanitize($_POST['dept']))) {
            $resp['response'] = "Department has not been provided. Please try again";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['amount']) || empty(Linter::sanitize($_POST['amount']))) {
            $resp['response'] = "Amount has not been provided. Please try again";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['accnt_name']) || empty(Linter::sanitize($_POST['accnt_name']))) {
            $resp['response'] = "Account name has not been provided. Please try again";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['accnt']) || empty(Linter::sanitize($_POST['accnt']))) {
            $resp['response'] = "Account has not been provided. Please try again";
            return Linter::jsonize($resp);
        }


        //everything is set, lets eval budget code {uniqueness: financial-year+department code}
        $budgetCode= str_replace('-','',Linter::sanitize($_POST['finYear'])).Linter::sanitize($_POST['dept']);


        $rs=Builder::table('budget')
            ->insert($budgetCode,Linter::sanitize($_POST['finYear']),
                Linter::sanitize($_POST['amount']),Linter::sanitize($_POST['dept']),
        Linter::sanitize($_POST['accnt_name']),Linter::sanitize($_POST['accnt']),
                Linter::sanitize(Carbon::now()))
            ->into('budgetCode','financialYear','amount',
                'departmentId','account','accntId','dateRecCreated');

        $rs=json_decode($rs,true);

        if($rs['status']=='success'){
            $resp=['status'=>'success','response'=>'Entry record added successfully.'];
            return Linter::jsonize($resp);
        }

        if($rs['status']=='error'&& $rs['code']==23000){//duplicate
            $resp['response']='Duplicate record found! Please try another department not in the list below';
            return Linter::jsonize($resp);
        }

        //else throw a general error
        $resp['response']='Sorry. An error occurred while saving the entry. '.
            'Please try again later or contact the webmaster';
        return Linter::jsonize($resp);
    }

    public static function loadBudgetEntries()
    {
        return json_decode(
            Builder::table('budgetoverview')
                ->get()
            ,true);
    }
    /**
     * Get the current financial year
     * @return bool|string
     */
    public static function getFinYear()
    {
        $rs = Builder::table('financialyears')
            ->select('financialYear')
            ->where('status', 1)
            ->get();
        $rs = json_decode($rs, true);

        if ($rs['status'] == 'success' && $rs['code'] == 200) {
            return $rs ['response'][0]['financialYear'];
        }
        return false;
    }


}