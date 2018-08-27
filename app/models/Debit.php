<?php

use Carbon\Carbon;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/10/2018
 * Time: 4:56 PM
 */

class Debit
{

    public $debitAccnt;
    private $amount;
    private $descrip;
    private $folio;
    private $tDate;

    public function __construct($account, $amount, $description, $transactionCode, $date)
    {
        $this->debitAccnt = $account;
        $this->amount = $amount;
        $this->descrip = $description;
        $this->folio = $transactionCode;
        $this->tDate = $date;
    }

    public function subPosting($financialYear)
    {
        if (self::accountExist($this->debitAccnt)==1){
            //do the posting
            $rs=Builder::table('genledger')
                ->insert(
                    $financialYear,$this->debitAccnt,
                    $this->amount,$this->calcRunningBal(),$this->tDate,
                    $this->descrip,Carbon::now())
                ->into(
                    'financialYear','accntCode','debit',
                    'runningBal','transactionDate','details','dateRecCreated'
                );

            $rs=json_decode($rs,true);
            if($rs['status']=='success'){return true;}

            return false;
        }

        return false;
    }


    private static function accountExist($accnt)
    {
        $r=Builder::table('chartofaccnts')
            ->select('accntCode')
            ->where('accntCode',$accnt)
            ->get();
        $r=json_decode($r,true);

        if($r['status']=='error'){return -1;} //account
        if($r['status']=='success' && $r['code']==300){return -1;} //account not yet added
        if($r['status']=='success' && $r['response'][0]['accntCode']==$accnt){return 1;} //account exist
        return -1;
    }

    private function calcRunningBal()
    {
        $res=Builder::table('genledger')
            ->select('runningBal')
            ->where('accntCode',$this->debitAccnt)
            ->orderBy('dateRecCreated')
            ->get(1);

        $res=json_decode($res,true);
        if($res['status']=='success'){

            return $res['code']==300?$this->amount: ($res['response'][0]['runningBal']+$this->amount); //add the amount
        }
        return null; //error occurred
    }

}