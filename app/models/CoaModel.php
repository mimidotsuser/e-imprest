<?php

use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/9/2018
 * Time: 8:34 AM
 */
class CoaModel
{
    /**
     * Get all accounts types {liabilities,income,expenses,assets,equity}
     * @return mixed
     */
    public static function getAcctTypes()
    {
        return json_decode(
            Builder::table('accntstype')
                ->get()
            , true);
    }

    /**
     *Get all registered accounts in the chart of accounts
     */
    public static function getAccnts()
    {
        return json_decode(
            Builder::table('chartofaccnts')
                ->orderBy('dateRecCreated', 'ASC')
                ->get()
            , true);
    }

    /**
     * Get account record details using department and the parent account code
     * @param $department
     * @param $accnt
     * @return mixed
     */
    public static function getAccntByDepart($department, $accnt)
    {

        $dat= json_decode(
            Builder::table('chartofaccnts')
                ->where('departmentId', Linter::sanitize($department))
                ->andWhere('parentAccnt', Linter::sanitize($accnt))
                ->get()
            , true);


        return$dat['response'][0]['accntCode'];
    }

    /**
     *Add new account
     */
    public static function add()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['name']) || empty(Linter::sanitize($_POST['name']))) {
            $resp['response'] = "Account name has not been provided. Please try again";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['descrip']) || empty(Linter::sanitize($_POST['descrip']))) {
            $resp['response'] = "Description has not been provided. Please try again";
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['type']) || empty(Linter::sanitize($_POST['type']))) {
            $resp['response'] = "Account type has not been provided. Please try again";
            return Linter::jsonize($resp);
        }

        //now lets add the account [first get the code to assign to the account]
        $accntCode = self::getMyAccntCode(Linter::sanitize($_POST['type']));

        $parentCode = isset($_POST['parent']) ? Linter::sanitize($_POST['parent']) : null;
        $dept = isset($_POST['dept']) ? Linter::sanitize($_POST['dept']) : 0;

        //then save the record
        $res = Builder::table('chartofaccnts')
            ->insert(TokensModel::genAccntCode(), $accntCode,
                Linter::sanitize($_POST['name']), Linter::sanitize($_POST['type']),
                Linter::sanitize($_POST['descrip']), $parentCode,
                Linter::sanitize($dept),Linter::sanitize(Carbon::now()))
            ->into('id', 'accntCode', 'name', 'accntTypeId', 'description',
                'parentAccnt', 'departmentId','dateRecCreated');

        $res = json_decode($res, true);
        if ($res['status'] == 'success') {
            $resp['status'] = 'success';
            $resp['response'] = 'account added successfully';
            return Linter::jsonize($resp);
        }
        //else
        $resp['response'] = 'Operation failed. Please contact the webmaster for more info';
        return Linter::jsonize($resp);
    }

    private static function getMyAccntCode($accntTypeId)
    {
        $d = Builder::table('accntstype')
            ->select('rangeCode')
            ->where('Id', $accntTypeId)
            ->get();
        $d = json_decode($d, true);

        if ($d['status'] == 'success' && $d['code'] == 200) {
            $range = $d['response'][0]['rangeCode'];

            $min = explode('-', $range)[0];
            $max = explode('-', $range)[1];
            //select last account within range

            $des = Builder::table('chartofaccnts')
                ->select('accntCode')
                ->where('accntCode', '>=', $min)
                ->andWhere('accntCode', '<=', $max)
                ->orderBy('accntCode', 'DESC')
                ->get(1);
            $des = json_decode($des, true);

            if ($des['status'] == 'success') {
                if ($des['code'] == 300) { //there is no account that has been added
                    return $min; //just return the first account
                }
                //return the account plus 1
                return ($des['response'][0]['accntCode']) + 1;
            }

        }
        return 1; //this means the type submitted is not valid!! system bypass detected
    }
}