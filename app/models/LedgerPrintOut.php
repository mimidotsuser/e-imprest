<?php

use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/21/2018
 * Time: 5:45 PM
 */

class LedgerPrintOut  extends FPDF
{
    private $refNo='';
    private  $title='';

    function Header()
    {
        $this->Image(  'public/images/logo.png', 90, 5, 32, 30);
        $this->SetFont('Times', 'B', 13);

        $this->Ln(6);
        $this->Cell(296, 10, "Ref:".$this->refNo, 0, 0, 'C');


        $this->Ln(20);
        $this->SetFont('Times', 'B', 18);

        $this->Cell(190, 10, "KISII UNIVERSITY", 0, 1, 'C');

        $this->SetFont('Times', 'B', 11);
        $this->Cell(190, 10, $this->title, 0, 1, 'C');
        $this->Ln(10);
    }

    private function legder($financialYear)
    {
        $this->AddPage();

        $v=null;
        //select all accounts
        $rsp=Builder::table('chartofaccnts')
            ->where('departmentId',0)
            ->orderBy('accntCode','ASC')
            ->get();
        $rsp=json_decode($rsp,true);
        if($rsp['code']==200){

            foreach ($rsp['response'] as $parentAccnt){

                $this->SetFont('Times', 'B', 11);

                //create top most cell for the account
                $this->Cell(125,20,'ACCOUNT NAME: '.$parentAccnt['name'],1,0);
                $this->Cell(56,20,'ACCOUNT CODE: '.$parentAccnt['accntCode'],1,1);


                $this->Ln(10);

                $this->subAccount($parentAccnt['accntCode'],$financialYear);

            }
        }

    }


    private function subAccount($parentAccnt,$finYear)
    {
        $rsp=Builder::table('ledgeraccntdetails')
            ->where('parentaccnt',$parentAccnt)
            ->andWhere('financialYear',$finYear)
            ->get();

        $rsp=json_decode($rsp,true);
        if($rsp['code']==200){

            foreach ($rsp['response'] as $details){

                $this->SetFont('Times', 'B', 12);

                //create top most cell for the account
                $this->Cell(124,20,'ACCOUNT NAME: '.$details['name'],1,0);
                $this->Cell(56,20,'ACCOUNT CODE: '.$details['accntCode'],1,1);

                $this->SetFont('Times','B','10');
                $this->Cell(30,10,'Date: ',1,0);
                $this->Cell(66,10,'Description ',1,0);
                $this->Cell(28,10,'Debit ',1,0);
                $this->Cell(28,10,'Credit ',1,0);
                $this->Cell(28,10,'Balance ',1,1);


                //get all transactions in this account
                self::getAccntTransactions($details['accntCode'],$finYear);

                $this->Ln(25);
            }

        }

    }

    private  function getAccntTransactions($accntCode,$finYear)
    {
        $rsp=Builder::table('ledgeraccntdetails')
            ->where('accntCode',$accntCode)
            ->andWhere('financialYear',$finYear)
            ->orderBy('dateRecCreated','ASC')
            ->get();

        $rsp=json_decode($rsp,true);
        if($rsp['code']==200) {

            foreach ($rsp['response'] as $trans) {

                $this->SetFont('Times','','12');
                $this->Cell(30,10,$trans['transactionDate'],1,0);
                $this->Cell(66,10,$trans['transacdetails'],1,0);
                $this->Cell(28,10,$trans['debit'],1,0);
                $this->Cell(28,10,$trans['credit'],1,0);
                $this->Cell(28,10,$trans['runningBal'],1,1);

            }
        }

    }


    public static function genLedger()
    {

        $financialYear=$_GET['id'];

        $n = new LedgerPrintOut();

        $n->AliasNbPages(); //page numbers

        $n->title='General Ledger Main Report';

        $n->refNo='KSU/FIN/ML/14';
        $n->legder($financialYear);
        $n->Output();
    }


    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

}