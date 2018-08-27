<?php

use Model\Linter;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/14/2018
 * Time: 8:19 AM
 */

class PrintOut extends FPDF
{

    private $refNo='KSU/FIN/CSF/04';
    private  $title='IMPREST APPLICATION FORM';
    private $baseStorageUrl='storage/imprest/printouts/';
    private $basesignsUrl='storage/signatures/';




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

    private function chartOfAccounts()
    {
        $this->AddPage();
        $this->SetFont('Times','',12);
        $rs=Builder::table('chartofaccnts')
            ->get();
        $rs=json_decode($rs,true);
        if($rs['status']=='success'){
            $this->SetFont('Times','B',12);
            $this->Cell(40, 10, "ACCOUNT CODE", 1, 0, 'L');
            $this->Cell(120, 10, "ACCOUNT NAME", 1, 0, 'C');
            $this->Cell(120, 10,"ACCOUNT DESCRIPTION", 1, 1, 'C');

            $this->SetFont('Times','',12);
            foreach ($rs['response'] as $account){
                $this->Cell(40, 10, $account['accntCode'], 1, 0, 'L');
                $this->Cell(120, 10, $account['name'], 1, 0, 'L');
                $this->Cell(120, 10, $account['description'], 1, 1, 'L');
            }
        }


    }

    private function budget($financialYear)
    {
        $this->AddPage();
        $this->SetFont('Times','B',12);

        $this->Cell(90, 10, "Department", 1, 0, 'C');
        $this->Cell(60, 10, "Account", 1, 0, 'C');
        $this->Cell(40, 10,"Amount ", 1, 1, 'C');

        $this->SetFont('Times','',12);

        $rs=Builder::table('budgetoverview')
            ->where('financialYear',$financialYear)
            ->get();

        $rs=json_decode($rs,true);
        if($rs['status']=='success' && $rs['code']==200){
            foreach ($rs['response'] as $account){
                $this->Cell(90, 10, $account['name'], 1, 0, 'L');
                $this->Cell(60, 10, $account['account'], 1, 0, 'L');
                $this->Cell(40, 10, 'KSH '.$account['amount'], 1, 1, 'L');
            }
        }
    }

    private static function fetchImprestDetails($imprestId)
    {
        return json_decode(
            Builder::table('requestdetails')
                ->orderBy('dateProcessed','ASC')
                ->get()
            ,true);
    }
    /**
     * Generate imprest request form
     * @param $imprestId
     */
    private function imprestRequest($imprestId)
    {
        $b=0; //labels border
        $t=0; //fill in data border
        $sect=0;
        $alt='B'; //fill in data font style
        $lbl='';

        $this->AddPage();

        $request=self::fetchImprestDetails($imprestId);

        if($request['code']!=200){return;}

        $request=$request['response'];

        $applicant=UsersModel::getUserDetails($request[0]['applicant']);

        if($applicant['code']!=200){return;}

        $applicant=$applicant['response'][0];

        $this->SetFont('Times',$lbl,12);
        $this->Cell(35, 10, 'NAME:', $t, 0, 'L');
        $this->SetFont('Times',$alt,13);
        $this->Cell(85, 10, $applicant['firstname'].' '.$applicant['lastname'].' '.$applicant['surname'], $b, 0, 'L');

        $this->SetFont('Times',$lbl,12);
        $this->Cell(40, 10, 'IMPREST NO :', $b, 0, 'L');
        $this->SetFont('Times',$alt,12);
        $this->Cell(20, 10, strtoupper($imprestId), $b, 1, 'L');



        $this->SetFont('Times',$lbl,12);
        $this->Cell(35, 10, 'DESIGNATION:', $b, 0, 'L');
        $this->SetFont('Times',$alt,12);
        $this->Cell(85, 10, $applicant['role'], $t, 0, 'L');

        $this->SetFont('Times',$lbl,12);
        $this->Cell(40, 10, 'PAYROLL NO:', $b, 0, 'L');
        $this->SetFont('Times',$alt,12);
        $this->Cell(20, 10, $applicant['staffnumber'], $t, 1, 'L');



        $this->SetFont('Times',$lbl,12);
        $this->Cell(35, 10, 'Department:', $b, 0, 'L');
        $this->SetFont('Times',$alt,12);
        $this->Cell(85, 10, $applicant['department'], $t, 0, 'L');

        $this->SetFont('Times',$lbl,12);
        $this->Cell(40, 10, 'GRADE:', $b, 0, 'L');
        $this->SetFont('Times',$alt,12);
        $this->Cell(20, 10, '', $t, 1, 'L');


        $this->SetFont('Times','',12);
        $this->MultiCell(180,15,'I hereby apply for Standing/ Temporary/ Special  imprest of Kshs '.$request[0]['initAmount'],$sect);
        $this->MultiCell(180,10,"NATURE OF DUTY : \n".$request[0]['description'],$sect);

        $this->MultiCell(180,10,"PROPOSED ITINERARY: \n ".$request[0]['itinerary'],$sect);
        $this->Cell(180, 10, 'Numbers of days to be away: '.$request[0]['est'].' days', $t, 1, 'L');

        $this->MultiCell(180,10,"I Certify that I have no previous outstanding Imprest",$sect);
        $this->Cell(30, 15, 'Signature', $t, 0, 'L');

        $this->Cell(80, 15, '', $t, 0, 'L');

        $this->Image($this->basesignsUrl.$applicant['screenId'].'.png',40,180,80,20);

        $this->Cell(30, 15, 'Date', $t, 0, 'L');
        $this->Cell(40, 15, $request[0]['dateRequested'], $t, 1, 'L');

//------------------------------------------------------------------------
//                       HOD section {request level 1}

        $this->SetFont('Times','B',12);
        $this->Cell(180, 10, 'Head OF Department', $t, 1, 'C');

        $this->SetFont('Times','',12);

        $this->MultiCell(180,10,"I recommend the imprest shown above against departmental accounts",$sect);

        $this->Cell(30, 15, 'Signature', $t, 0, 'L');
        $this->Cell(80, 15, '', $t, 0, 'L');

        $this->Image($this->basesignsUrl.$request[0]['designee'].'.png',40,218,80,20);

        $this->Cell(30, 15, 'Date', $t, 0, 'L');
        $this->Cell(40, 15, $request[1]['dateProcessed'], $t, 1, 'L');

//-------------------------------------------------------------------------------------
//                          Accounts Claimant Section (level 2)
        $this->SetFont('Times','B',12);
        $this->Cell(180, 10, 'ACCOUNTANT (CLAIMANT SECTION )', $t, 1, 'C');

        $this->SetFont('Times','',12);

        $this->MultiCell(180,10,"I certify that  applicant has no withstanding imprest",$sect);

        $this->Cell(30, 15, 'Signature', $t, 0, 'L');
        $this->Cell(80, 15, '', $t, 0, 'L');

        $this->Image($this->basesignsUrl.$request[1]['designee'].'.png',40,256,80,20);

        $this->Cell(30, 15, 'Date', $t, 0, 'L');
        $this->Cell(40, 15, $request[2]['dateProcessed'], $t, 1, 'L');

//---------------------------------------------------------------------------------------------
//                ACCOUNTANT(EXAMINER-VOTEBOOK SECTION)
        $this->SetFont('Times','B',12);
        $this->Cell(180, 10, 'ACCOUNTANT (VOTEBOOK SECTION )', $t, 1, 'C');

        $this->SetFont('Times','',12);

        $this->MultiCell(180,10,"I certify that department has the ability to meet the expenditure",$sect);

        $this->Cell(30, 15, 'Signature', $t, 0, 'L');
        $this->Cell(80, 15, '', $t, 0, 'L');

        $this->Image($this->basesignsUrl.$request[2]['designee'].'.png',40,80,80,20);

        $this->Cell(30, 15, 'Date', $t, 0, 'L');
        $this->Cell(40, 15, $request[3]['dateProcessed'], $t, 1, 'L');


//-----------------------------------------------------------------------------------------
//                        VC/DVC/REGGISTRAR SECTION (level 4)

        $lvl3Designee=UsersModel::getUserDetails($request[3]['designee'])['response'][0];
        $name=$lvl3Designee['firstname'].' '.$lvl3Designee['lastname'].' '.$lvl3Designee['surname'];

        $this->SetFont('Times','B',12);
        $this->Cell(180, 10, 'VC/DVC (A,P&F)/REGISTRAR ADMIN', $t, 1, 'C');

        $this->SetFont('Times','',12);

        $this->Cell(50,10,"Approved ",$sect,0,'C');
        $this->Cell(130,10,$name,$sect,1,'L');

        $this->Cell(30, 15, 'Signature', $t, 0, 'L');
        $this->Cell(80, 15, '', $t, 0, 'L');

        $this->Image($this->basesignsUrl.$request[3]['designee'].'.png',40,109,80,20);

        $this->Cell(30, 15, 'Date', $t, 0, 'L');
        $this->Cell(40, 15, $request[4]['dateProcessed'], $t, 1, 'L');

//-------------------------------------------------------------------------------------------------------
//          FO (level 5)
        $this->SetFont('Times','B',12);
        $this->Cell(180, 10, 'F.O./DFO/SENIOR ACCOUNTANT', $t, 1, 'C');

        $this->SetFont('Times','',12);

        $this->Cell(70,10,"Approved for payment of Ksh",$sect,0,'L');
        $this->Cell(110,10, $request[5]['amntApproved'],$sect,1,'L');

        $this->Cell(30, 15, 'Signature', $t, 0, 'L');
        $this->Cell(80, 15, '', $t, 0, 'L');

        $this->Image($this->basesignsUrl.$request[4]['designee'].'.png',40,145,80,20);

        $this->Cell(30, 15, 'Date', $t, 0, 'L');
        $this->Cell(40, 15, $request[5]['dateProcessed'], $t, 1, 'L');

//---------------------------------------------------------------------------------------------
//                    AUDIT USE ONLY

        $this->Ln(10);
        $this->SetFont('Times','B',12);
        $this->Cell(180, 10, 'FOR AUDIT USE ONLY', $t, 1, 'C');

        $this->SetFont('Times','',12);

        $this->MultiCell(180,10,"The regulations governing the Imprest warrant authorization have been followed and  to the best of knowledge the charges thereon are correct.",$sect);

        $this->Cell(30, 15, 'Signature', $t, 0, 'L');
        $this->Cell(80, 15, '', $t, 0, 'L');

        $this->Cell(30, 15, 'Date', $t, 0, 'L');
        $this->Cell(40, 15, '', $t, 1, 'L');

    }


    public function imprestSurrender(){

    }

    public static function genImprestRequest($imprestId)
    {
        $n = new PrintOut();
        $n->AliasNbPages(); //page numbers

        $n->title='IMPREST REQUEST FORM';

        $n->refNo=empty($refNo)?$n->refNo:$refNo;

        $n->imprestRequest($imprestId);
        $n->Output($n->baseStorageUrl.$imprestId.'.pdf','F');
    }

    public static function genImprestSurrender($imprestId, $applicationref,$refNo='')
    {
        $n = new PrintOut();
        $n->AliasNbPages(); //page numbers

        $n->title='IMPREST SURRENDER FORM';
        $n->refNo=empty($refNo)?$n->refNo:$refNo;

        $n->imprestSurrender();
        $n->Output($n->baseStorageUrl.$applicationref.'.pdf','F');
    }

    public static function genChartOfAccntsReport()
    {

        $n = new PrintOut("l");

//        $n->SetMargins(0,1,0);

        $n->AliasNbPages(); //page numbers

        $n->title='Chart Of Accounts Report';

        $n->refNo='KSU/FIN/COA/04';
        $n->chartOfAccounts();
        $n->Output();
    }



    public static function genBudgetReport()
    {
        $n = new PrintOut();
//        $n->SetMargins(0,1,0);

        $n->AliasNbPages(); //page numbers

        $financialYear=isset($_GET['id'])||!empty($_GET['id'])?Linter::sanitize($_GET['id']):'2017-2018';
        $n->title=$financialYear.' Budget Allocation Report';

        $n->refNo='KSU/FIN/B/04';
        $n->budget($financialYear);
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