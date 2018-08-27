<?php

use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/9/2018
 * Time: 8:08 AM
 */
class LedgerModel
{

    public static function initStartYear($yearId)
    {
        $imprestAccount = 5000;
        $cashAccnt=1100;

        //check if financial year budget {for imprest account } has been set

        $rs1 = Builder::table('budget')
            ->select('count(id) as ct')
            ->where('financialYear', $yearId)
            ->andWhere('accntId', $imprestAccount)//imprest only
            ->get();
        $rs1 = json_decode($rs1, true);
        if ($rs1['status'] != 'success' && $rs1['code'] != 200) {
            //discontinue, throw error
            return "The budget for financial year " . $yearId . ' has not been set.';
        }
        if ($rs1['response'][0]['ct'] != 16) {
            //discontinue, throw error, first the user should set the budget {imprest for all departments}
            return 'The financial year budget for Imprest account has not been set for all departments';
        }

        $rs2 = Builder::table('budget')
            ->select('departmentId', 'amount')
            ->where('financialYear', $yearId)
            ->andWhere('accntId', $imprestAccount)
            ->get();
        $rs2 = json_decode($rs2, true);
        if ($rs2['code'] == '200') {


            foreach ($rs2['response'] as $d) { //for each department imprest budget

                $subAccnt1Id = CoaModel::getAccntByDepart($d['departmentId'], $imprestAccount); //fetch the sub account first
                $subAccnt2Id = CoaModel::getAccntByDepart($d['departmentId'], $cashAccnt); //fetch the sub account first

                //post to credit account
                $temp = new Debit($subAccnt1Id, $d['amount'], 'Start of year initializing',
                    TokensModel::genTranscCode(),
                    Linter::sanitize(Carbon::now())
                );

                $temp->subPosting($yearId);

                $tmp=new  Credit($subAccnt2Id,0,'',
                    TokensModel::genTranscCode(),Linter::sanitize(Carbon::now()));

                $tmp->subPosting($yearId);

            }

            return null;
        }
        return "Error while accessing the " . $yearId . ' budget.';
    }


    /**
     * @param Credit $creditable
     * @param Debit $debitable
     * @return array
     */
    public static function post(Credit $creditable, Debit $debitable)
    {

        $resp = ['status' => 'error', 'response' => 'Transaction could not be posted as the appear to be null.'];

        if (is_null($creditable) && is_null($debitable)) { //if both transactions are  null
            return $resp;
        }

        $finYear = FinanceModel::getFinYear();

        if (is_bool($finYear)) {
            $resp['response'] = "Transaction not posted. The current financial year has not been set.";
            return $resp;
        }

        //if one account is not handled, post to the other transact to error account
        if (!is_a($debitable, "Debit") || is_null($debitable)) {
            $creditable->creditAcct = '0000';

            //thus credit transaction has been set, lets post it now to error account
            if ($creditable->subPosting($finYear)) {
                $resp = ['status' => 'success', 'response' => 'Transaction posted to the error account for reconciliation'];
                return $resp;
            } else {
                $resp = ['status' => 'error',
                    'response' => 'Transacted could not be posted to the credit account. Please contact the webmaster'];
                return $resp;
            }
        }

        if (!is_a($creditable, "Credit") || is_null($creditable)) {
            $debitable->debitAccnt = '0000';

            //thus debit transaction was provided, lets post it
            if ($debitable->subPosting($finYear)) {
                $resp = ['status' => 'success', 'response' => 'Transaction posted to the error account for reconciliation'];
                return $resp;
            } else { //an error occurred
                $resp = ['status' => 'error',
                    'response' => 'Transacted could not be posted to the debit account. Please contact the webmaster'];
                return $resp;
            }
        }

        //if we are here, transactions details were provided

        if ($creditable->subPosting($finYear)) { //credit the account
            if ($debitable->subPosting($finYear)) {//debit the account
                $resp = ['status' => 'success', 'response' => 'Transaction posted successfully'];
                return $resp;
            } else {
                $resp = ['status' => 'error',
                    'response' => 'Transacted could only be posted to the credit account. Please contact the webmaster'];
                return $resp;
            }
        } else {
            $resp = ['status' => 'error',
                'response' => '!# Transacted could not be posted. Please contact the webmaster'];
            return $resp;
        }
    }


    /**
     * Get the amount assigned for a certain financial for a department
     * @param $department
     * @param $account
     * @return mixed
     */
    public static function getFinancialYrBudget($department, $account)
    {
        $yr= FinanceModel::getFinYear();

        return json_decode(
            Builder::table('ledgeraccntdetails')
                ->where('financialYear',$yr)
                ->andWhere('parentaccnt',Linter::sanitize($account))
                ->andWhere('departmentId', Linter::sanitize($department) )
                ->andWhere('debit','<>',null) //where debit is not null
                ->get()
            , true);
    }

    public static function ledgerAccntDetails()
    {
        return json_decode(Builder::rawSelect(
            'SELECT  ledgeraccntdetails.financialYear,ledgeraccntdetails.accntCode,'.
            ' ledgeraccntdetails.name as accntname, budgetoverview.amount, ledgeraccntdetails.runningBal'.
            ' FROM ledgeraccntdetails INNER JOIN budgetoverview ON'.
            ' ledgeraccntdetails.departmentId = budgetoverview.departmentId AND'.
            ' ledgeraccntdetails.parentaccnt=budgetoverview.accntId'),
            true);
    }
}


