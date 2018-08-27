<?php

use Carbon\Carbon;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/10/2018
 * Time: 4:56 PM
 */
class Credit
{
    public $creditAcct;
    private $amount;
    private $descrip;
    private $folio;
    private $tDate;

    /**
     * Credit constructor.
     * @param $account
     * @param $amount
     * @param $description
     * @param $transactionCode
     * @param $date
     */
    public function __construct($account, $amount,$description,$transactionCode,$date)
    {
        $this->creditAcct=$account;
        $this->amount=$amount;
        $this->descrip=$description;
        $this->folio=$transactionCode;
        $this->tDate=$date;
    }

    public function subPosting($financialYear)
    {
        if (self::accountExist($this->creditAcct)==1){
            //do the posting
            $rs=Builder::table('genledger')
                ->insert(
                    $financialYear,$this->creditAcct,
                    $this->amount,$this->calcRunningBal(),$this->tDate,
                    $this->descrip,Carbon::now())
                ->into(
                    'financialYear','accntCode','credit',
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
            ->where('accntCode',$this->creditAcct)
            ->orderBy('dateRecCreated')
            ->get(1);

        $res=json_decode($res,true);
        if($res['status']=='success'){

            return $res['code']==300?null: ($res['response'][0]['runningBal']-$this->amount);
        }
        return null; //error occurred
    }

}