<?php

use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/13/2018
 * Time: 6:02 PM
 */
class ImprestManager extends ImprestModel
{


    /**
     * @return mixed
     */
    public static function applyRequest()
    {

        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['descript']) || empty(Linter::sanitize($_POST['descript']))) {
            $resp['response'] = "Description has not been provided";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['amount']) || empty(Linter::sanitize($_POST['amount']))) {
            $resp['response'] = "Amount has not been provided";
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['est'])) {
            $resp['response'] = "Estimated days away has not been provided. Enter 0 if not applicable";
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['expDate']) || empty(Linter::sanitize($_POST['expDate']))) {
            $resp['response'] = "The expected date has not been provided";
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['surDate']) || empty(Linter::sanitize($_POST['surDate']))) {
            $resp['response'] = "The surrender date has not been indicated";
            return Linter::jsonize($resp);
        }


        $userdata = AppGlobalsModel::currentUserDetails()['response'][0]; //applicants data==current userdata

        //if the request exceeds the budget, just return an error
        if (self::hasExceededBudget(Linter::sanitize($_POST['amount']), $userdata['departmentId'])) {
            $resp['response'] = "Sorry the request could not be processed as it exceeds the current department budget";
            return Linter::jsonize($resp);
        }

        //check if the applicant has more than 2 un-surrendered requests
        $edos=self::has2PendingSurrenders($userdata['screenId']);

        if(is_null($edos)){
            $resp['response'] = "Sorry the request could not be processed. Internal Server Error";
            return Linter::jsonize($resp);
        }

        if($edos){
            $resp['response'] = "Sorry the request could not be processed as you have two request pending surrender";
            return Linter::jsonize($resp);
        }


        $requestLevel = 1; //at which level is the request

        //fetch the approving staff at this request level i.e the HOD
        $temp = ImprestModel::getWarrantApprovingStaff(
            $userdata['departmentId'], $requestLevel, $userdata['parentDepartment'], $userdata['roleId']
        );

        if ($temp['status'] != 'success') {
            $resp['response'] = 'Sorry. System is experiencing unexpected errors. Please try again later';
            return Linter::jsonize($resp);
        }

        $approvingStaff = $temp['response'][0]['screenId']; //get the approving staff screen id

        $imprestId = self::getImprestId();

        $attached = isset($_FILES['attachment']) && !empty($_FILES['attachment']['name']) ? 1 : 0; //check if there is an attachment

        $attachment='';
        //lets save attachment first if any (as it can be overwritten in case of request failure)
        if ($attached > 0) {
            //{check first if we missed something }
            if ($_FILES["attachment"]["error"] != 0) {
                $resp['response'] = "Recommendation could not be uploaded successfully. Please try again.";
                return Linter::jsonize($resp);
            }
            $attachment=  $imprestId.'.'.pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION); //get the attachment

            $dff = self::saveRecommendation($imprestId, $_FILES['attachment']);
            if ($dff != null) {
                return Linter::jsonize($dff);
            }
        }

        //get the current active financial year
        $finYear=FinanceModel::getFinYear();
        //save the request in imprest requests table
        $rs1 = Builder::table('imprestrequests')
            ->insert(
                $imprestId,$finYear, $userdata['departmentId'], $userdata['screenId'],
                Linter::sanitize($_POST['descript']), Linter::sanitize($_POST['itinerary']),
                Linter::sanitize($_POST['est']), Linter::sanitize($_POST['amount']), $attachment,
                Linter::sanitize($_POST['expDate']), Linter::sanitize($_POST['surDate']),
                Linter::sanitize(Carbon::now())
            )
            ->into('ImprestId','financialYear', 'departmentId', 'screenId',
                'description', 'itinerary', 'est', 'amount', 'attachmentsAvail', 'expectedDate',
                'surrenderDate', 'dateRecCreated');

        $rs1 = json_decode($rs1, true);


        if ($rs1['status'] != 'success') {
            $resp['response'] = "Error processing your request.
             Please contact the administrator for support";
            return Linter::jsonize($resp);
        }

        //if above was successful, save it to imprest request approvals table with pending status


        $rs2 = Builder::table('requestsapprovals')
            ->insert($imprestId, Linter::sanitize($_POST['amount']), $approvingStaff, 0, $requestLevel,
                Linter::sanitize(Carbon::now()))
            ->into('imprestId', 'amount', 'screenId', 'status', 'requestLevel', 'dateRecCreated');

        $rs2 = json_decode($rs2, true);

        if ($rs2['status'] == 'success') {
            $resp = ['status' => 'success', 'response' => 'Request has been submitted successfully. 
            You can track the approval progress under reports menu'];

            return Linter::jsonize($resp);
        }

        //saving to the imprest approvals failed!!!!
        $resp['response'] = 'Error processing your request. Please contact the administrator for further support';
        return Linter::jsonize($resp);

    }


    public static function applySurrender()
    {

        $resp = ['status' => 'error', 'response' => "Request broken. Sorry please try again."];

        if (!isset($_POST['imprestId']) || empty(Linter::sanitize($_POST['imprestId']))) {
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['amntAssigned']) || empty(Linter::sanitize($_POST['amntAssigned']))) {
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['refundAmnt']) || Linter::sanitize($_POST['refundAmnt'])<0) {
            $resp['response']='Request failed due to unknown amount to refund. Please try again.';
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['claimingAmnt']) || Linter::sanitize($_POST['claimingAmnt'])<0) {
            $resp['response']='Request failed due to unknown amount being claimed. Please try again.';

            return Linter::jsonize($resp);
        }
        if (!isset($_FILES['uploads']) || $_FILES["uploads"]["error"] != 0) {
            $resp['response'] = "Upload was not successful. Please try again.";
            return Linter::jsonize($resp);
        }

        //everything is set {sanity check ✔}
        $refId=TokensModel::genImprestRefCode();

        $userdata = AppGlobalsModel::currentUserDetails()['response'][0];

        $attachment=  $refId.'.'.pathinfo($_FILES['uploads']['name'], PATHINFO_EXTENSION); //get the file extension

        //save the receipt first
        $dese=self::saveReceipt($refId,$_FILES['uploads']);
        if(!is_null($dese)){return Linter::jsonize($dese);}


        $requestLevel = 1; //at which level is the request

        //fetch the approving staff at this request level i.e the HOD
        $temp = ImprestModel::getWarrantApprovingStaff(
            $userdata['departmentId'], $requestLevel, $userdata['parentDepartment'], $userdata['roleId']
        );

        if ($temp['status'] != 'success') {
            $resp['response'] = 'Sorry. System is experiencing unexpected errors. Please try again later';
            return Linter::jsonize($resp);
        }

        $approvingStaff = $temp['response'][0]['screenId']; //get the approving staff screen id



        //post the request to surrender requests table
        $rs1=Builder::table('imprestsurrender')
            ->insert($refId,$userdata['screenId'],
                Linter::sanitize($_POST['imprestId']), Linter::sanitize($_POST['amntAssigned']),
                Linter::sanitize($_POST['refundAmnt']),Linter::sanitize($_POST['claimingAmnt']),
                $attachment,Linter::sanitize(Carbon::now()))
            ->into('refId','screenId','imprestId','amountAssigned',
                'amountRefunding','amountClaiming','attachmentUrl','dateRecCreated');

        $rs1=json_decode($rs1,true);

        if($rs1['code']!=200){ //an error occurred
            $resp['response']='Server is currently experiencing some errors. Please try again later';
            return Linter::jsonize($resp);
        }
        //else save it to surrender approval table with pending request
        $rs2=Builder::table('surrenderapprovals')
            ->insert($refId,$approvingStaff,'0',$requestLevel,Linter::sanitize(Carbon::now()))
            ->into('refId','screenId','status','requestLevel','dateRecCreated');
        $rs2=json_decode($rs2,true);

        if ($rs2['status']=='success'){
            $resp['status']='success';
            $resp['response']='Surrender application submitted successfully. You can check its appproval progress under reports menu';
       return Linter::jsonize($resp);
        }
        $resp['response']='Server is currently experiencing some errors. Please try again later';
            return Linter::jsonize($resp);

    }


    public static function fetchForSurrender()
    {
        return self::getImprestToSurrender();

    }
    /**
     *Fetch request which current user should approve (has status!=cancelled and not rejected)
     */
    public static function warrantApplicationsForApproval()
    {
        $user = AppGlobalsModel::currentUserDetails()['response'][0]; //current user details

        return self::fetchWarrantRequestToApprove($user['screenId']);
    }

    public static function surrenderApplicationsForApproval()
    {
        $user = AppGlobalsModel::currentUserDetails()['response'][0]; //current user details

        return self::fetchSurrenderRequestToApprove($user['screenId']);
    }

    public static function viewImprestRequestDetails()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['requestId']) || empty(Linter::sanitize($_POST['requestId']))) {
            return Linter::jsonize($resp);
        }
        return self::getImprestRequestDetails(Linter::sanitize($_POST['requestId']));

    }

    public static function imprestWarrantApproval()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];


        if (!isset($_GET['id']) || empty(Linter::sanitize($_GET['id']))) {
            return Linter::jsonize($resp);
        }


        if(Linter::sanitize($_GET['id'])=='request'){
            return ImprestModel::processRequisitionRequest();
        }

        if (Linter::sanitize($_GET['id'])=='request_acknowledge'){
            //this means complete warrant requisition process
             return ImprestModel::acknowledgeWarrant();
        }
    }

    public static function viewImprestSurrenderDetails()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['imprestId']) || empty(Linter::sanitize($_POST['imprestId']))) {
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['refId']) || empty(Linter::sanitize($_POST['refId']))) {
            return Linter::jsonize($resp);
        }

        return self::getImprestSurrenderDetails(Linter::sanitize($_POST['imprestId']),Linter::sanitize($_POST['refId']));
    }


    public static function imprestSurrenderApproval()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];


        if (!isset($_GET['id']) || empty(Linter::sanitize($_GET['id']))) {
            return Linter::jsonize($resp);
        }

        if(Linter::sanitize($_GET['id'])==1){
            return ImprestModel::processSurrenderRequest();
        }

        if (Linter::sanitize($_GET['id'])==2){
            //this means complete surrender process
            return ImprestModel::acknowledgeSurrender();
        }

    }

    public static function fetchAllMyImprestWarrantApplications()
    {
        $applicant=AppGlobalsModel::currentUserDetails()['response'][0]['screenId'];
        return json_decode(
            Builder::table('requeststatusdetails')
                ->where('applicant',$applicant)
                ->orderBy('id','DESC')
                ->get()
            ,true);
    }

    public static function fetchAllMyImprestSurrenderApplications()
    {
        $applicant=AppGlobalsModel::currentUserDetails()['response'][0]['screenId'];
        return json_decode(
            Builder::table('surrenderstatusdetails')
            ->where('applicant',$applicant)
                ->orderBy('id','DESC')
            ->get()
            ,true);
    }

    public static function fetchTimelineData()
    {
        $resp=['status'=>'error','response'=>'Error in the request. Please try again later'];

        if(!isset($_GET['id']) || empty($_GET['id'])){
            return Linter::jsonize($resp);
        }
        if(!isset($_POST['id']) || empty($_POST['id'])){
            return Linter::jsonize($resp);
        }

        if(Linter::sanitize($_GET['id'])==1){
            //fetch imprest request details
          return array_merge( json_decode(
              Builder::table('requestdetails')
              ->where('ImprestId',Linter::sanitize($_POST['id']))
              ->get()
              ,true),
              ['type'=>'1']//request type
           );

        }elseif (Linter::sanitize($_GET['id'])==2){
            return
                array_merge(json_decode(
                Builder::table('surrenderdetails')
                    ->where('refId',Linter::sanitize($_POST['id']))
                    ->get()
                ,true),
                ['type'=>'2']
                );
        }

        return Linter::jsonize($resp);

    }
}
