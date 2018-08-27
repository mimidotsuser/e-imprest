<?php

use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/11/2018
 * Time: 1:48 PM
 */
class ImprestModel
{

    /**
     *In case the request was approved, generate a printout
     */
    public static function acknowledgeWarrant()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['imprestId']) || empty(Linter::sanitize($_POST['imprestId']))) {
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['status'])) {
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['amount']) || empty(Linter::sanitize($_POST['amount']))) {
            return Linter::jsonize($resp);
        }
        //all set

        $status = Linter::sanitize($_POST['status']);


        if ($status == 0) { //if request was rejected, just post to the database
            $rs = Builder::table('requestsapprovals')
                ->insert(
                    Linter::sanitize($_POST['imprestId']),
                    null,
                    Linter::sanitize($_POST['amount']),
                    Linter::sanitize($_POST['comment']), '2',
                    Linter::sanitize($_POST['level']),
                    Linter::sanitize(Carbon::now())
                )
                ->into('imprestId', 'screenId', 'amount', 'comments', 'status', 'requestLevel', 'dateRecCreated');
            $rs = json_decode($rs, true);

            if ($rs['status'] == 'success') {
                $resp = ['status' => 'success', 'response' => 'Request processed successfully'];
                return Linter::jsonize($resp);
            }

            $resp['response'] = 'Request not fully processed successfully. Please try again.';
            return Linter::jsonize($resp);
        } elseif ($status == 1) {
            $rs = Builder::table('requestsapprovals')
                ->insert(
                    Linter::sanitize($_POST['imprestId']),
                    null,
                    Linter::sanitize($_POST['amount']),
                    Linter::sanitize($_POST['comment']), '4', //assign status of completion
                    Linter::sanitize($_POST['level']),
                    Linter::sanitize(Carbon::now())
                )
                ->into('imprestId', 'screenId', 'amount', 'comments', 'status', 'requestLevel', 'dateRecCreated');
            $rs = json_decode($rs, true);

            if ($rs['status'] == 'success') {

                //generate the printout
                PrintOut::genImprestRequest(Linter::sanitize($_POST['imprestId']));

                $resp = ['status' => 'success',
                    'response' => 'Request processed successfully. Click ' .
                        '<a href="' . env('APP_URL') . '/storage/imprest/printouts/' . Linter::sanitize($_POST['imprestId']) .
                        '.pdf" target="_blank" class="btn btn-info btn-sm"> download <i class="fa fa-download"></i> </a> to get the printout application form'];

                return Linter::jsonize($resp);
            }

            $resp['response'] = 'Request not processed successfully. Please try again.';
            return Linter::jsonize($resp);
        }
        //neva reach here!!
        $resp['response'] = 'Request processed. Please try again.';
        return Linter::jsonize($resp);
    }

    /**
     *If surrender request is fully approved, post the transaction to the general ledger, the generate a prinout
     *
     */
    public static function acknowledgeSurrender()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['refId']) || empty(Linter::sanitize($_POST['refId']))) {
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['status']) || empty(Linter::sanitize($_POST['status']))) {
            return Linter::jsonize($resp);
        }

        if(Linter::sanitize($_POST['status'])==2){ //request rejected

            //{just post the request}
            $rs=Builder::table('surrenderapprovals')
                ->insert(Linter::sanitize($_POST['refId']),null,
                    Linter::sanitize($_POST['comment']),'2','5',Linter::sanitize(Carbon::now()))
                ->into('refId','screenId','comments','status','requestLevel','dateRecCreated');
            $rs=json_decode($rs,true);
            if($rs['status']=='success'){
                $resp['status']='success';
                $resp['response']='Request successfully processed';
                return Linter::sanitize($resp);
            }

        }elseif (Linter::sanitize($_POST['status'])==1){

            $rs=Builder::table('surrenderapprovals')
                ->insert(Linter::sanitize($_POST['refId']),null,
                    Linter::sanitize($_POST['comment']),'4','5',Linter::sanitize(Carbon::now()))
                ->into('refId','screenId','comments','status','requestLevel','dateRecCreated');

            $rs=json_decode($rs,true);
            if($rs['status']=='error'){
                $resp['response']="Request could not be processed at this moment. Please try again later.";
                return Linter::sanitize($resp);
            }
            //else post the transaction to the general ledger
            //credit imprest account, credit cash account

            $imprestAccount = 5000;
            $cashAccnt=1100;

            $subAccnt1Id = CoaModel::getAccntByDepart(Linter::sanitize($_POST['departmentId']), $imprestAccount); //fetch the sub account first
            $subAccnt2Id = CoaModel::getAccntByDepart(Linter::sanitize($_POST['departmentId']), $cashAccnt); //fetch the sub account first

            $tmp=new  Credit($subAccnt1Id, $_POST['amount'],'Credited to cash account',
                Linter::sanitize($_POST['imprestId']),Linter::sanitize(Carbon::now()));

            $temp = new Debit($subAccnt2Id, $_POST['amount'], 'Debited from Imprest account',
                TokensModel::genTranscCode(),
                Linter::sanitize(Carbon::now())
            );


            LedgerModel::post($tmp,$temp);

            //generate imprest surrender form printout with application ref as its name.pdf
            PrintOut::genImprestSurrender( Linter::sanitize($_POST['imprestId']), Linter::sanitize($_POST['refId']));

            $resp = ['status' => 'success',
                'response' => 'Request processed successfully. Click ' .
                    '<a href="' . env('APP_URL') . '/storage/imprest/printouts/' . Linter::sanitize($_POST['refId']) .
                    '.pdf" target="_blank" class="btn btn-info btn-sm"> download <i class="fa fa-download"></i> </a> to get the printout application form'];

            return Linter::jsonize($resp);
        }

        //unknown error
        $resp['response']="Request could not be processed at this moment. Please try again later.";
        return Linter::sanitize($resp);
    }


    /**
     * Fetch the approving staff for imprest warrant at specific request level and each department
     * @param $department
     * @param $level
     * @param $parentDept
     * @param $applicantRole
     * @param int $amount
     * @return mixed
     */
    protected static function getWarrantApprovingStaff($department, $level, $parentDept, $applicantRole, $amount = 0)
    {
        switch ($level) {

            case 1:
                if ($applicantRole != 2) { //if not the head of the department, get the hod
                    return json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', $department)
                            ->andWhere('roleId', 2)//select user with HOD role
                            ->get(),
                        true);
                }
                //else if is the HOD,
                if ($department == 1) { //check if is VC?
                    //return the DVC  screen id
                    json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', 3)//or select ASA DVC
                            ->orWhere('departmentId', 2)//select AP&F DVC
                            ->andWhere('active', 1)//where account is active
                            ->andWhere('roleId', 2)
                            ->orderBy('departmentId', 'ASC')//increase probability of AP&F being first option
                            ->get(1),
                        true);
                } else {
                    //return HOD of the reporting department
                    return json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', $parentDept)
                            ->andWhere('roleId', 2)//select user with HOD role
                            ->get(),
                        true);
                }
                break;

            case 2: //select user with accountant claimant role
                return json_decode(
                    Builder::table('staffdetails')
                        ->select('screenId')
                        ->where('roleId', 3)
                        ->get(1)
                    , true);

                break;
            case 3://select user with accountant examiner role
                return json_decode(
                    Builder::table('staffdetails')
                        ->select('screenId')
                        ->where('roleId', 4)
                        ->get(1)
                    , true);
                break;
            case 4:
                //if amount is greater than 100k and applicant is not VC
                if ($amount > 100000 && ($applicantRole != 2 && $department != 1)) {

                    return json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', 1)//select VC
                            ->andWhere('roleId', 2)
                            ->get(),
                        true);
                } else {
                    //return dvc{either DVC registrar academics or DVC finance department}
                    return json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', 3)//or select ASA DVC
                            ->orWhere('departmentId', 2)//select AP&F DVC
                            ->andWhere('active', 1)//where account is active
                            ->andWhere('roleId', 2)
                            ->orderBy('departmentId', 'ASC')//increase probability of AP&F being first option
                            ->get(1),
                        true);

                }
                break;
            case 5:
                //select the FO

                return json_decode(
                    Builder::table('staffdetails')
                        ->select('screenId')
                        ->where('roleId', 2)
                        ->andWhere('departmentId', 7)//finance department
                        ->get(1)
                    , true);

                break;
        }

    }


    /**
     * Fetch the approving staff for imprest surrender at specific request level and each department
     * @param $department
     * @param $level
     * @param $parentDept
     * @param $applicantRole
     * @param int $amount
     * @return mixed
     */
    protected static function getSurrenderApprovingStaff($department, $level, $parentDept, $applicantRole, $amount = 0)
    {
        switch ($level) {

            case 1:
                if ($applicantRole != 2) { //if not the head of the department, get the hod
                    return json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', $department)
                            ->andWhere('roleId', 2)//select user with HOD role
                            ->get(),
                        true);
                }
                //else if is the HOD,
                if ($department == 1) { //check if is VC?
                    //return the DVC (AP&F) or DVC(ASA) screen id depending if account is active
                    json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', 3)//or select ASA DVC
                            ->orWhere('departmentId', 2)//select AP&F DVC
                            ->andWhere('active', 1)//where account is active
                            ->andWhere('roleId', 2)
                            ->orderBy('departmentId', 'ASC')//increase probability of AP&F being first option
                            ->get(1),//limit by one
                        true);
                } else {
                    //return HOD of the reporting department
                    return json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', $parentDept)
                            ->andWhere('roleId', 2)//select user with HOD role
                            ->get(),
                        true);
                }
                break;

            case 2: //select user with accountant examiner role
                return json_decode(
                    Builder::table('staffdetails')
                        ->select('screenId')
                        ->where('roleId', 4)
                        ->get(1)
                    , true);

                break;
            case 3:
                //if amount is greater than 100k and applicant is not VC
                if ($amount > 100000 && ($applicantRole != 2 && $department != 1)) {

                    return json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', 1)//select VC
                            ->andWhere('roleId', 2)
                            ->get(),
                        true);
                } else {
                    //return dvc{either DVC registrar academics or DVC finance department}
                    return json_decode(
                        Builder::table('staffdetails')
                            ->select('screenId')
                            ->where('departmentId', 3)//or select ASA DVC
                            ->orWhere('departmentId', 2)//select AP&F DVC
                            ->andWhere('active', 1)//where account is active
                            ->andWhere('roleId', 2)
                            ->orderBy('departmentId', 'ASC')//increase probability of AP&F being first option
                            ->get(1),
                        true);

                }

                break;
            case 4:
                //select user with accountant at vote book section (claims)
                return json_decode(
                    Builder::table('staffdetails')
                        ->select('screenId')
                        ->where('roleId', 3)
                        ->get(1)
                    , true);
                break;
            case 5:
                //select the F.O./DFO/SENIOR ACCOUNTANT  {{FO for now}}

                return json_decode(
                    Builder::table('staffdetails')
                        ->select('screenId')
                        ->where('roleId', 2)
                        ->andWhere('departmentId', 7)//finance department
                        ->get(1)
                    , true);

                break;
        }

    }


    /**
     * Get temporarily balance on spending on imprest (i.e pending request+fully approved rquests amount)
     * @param $department
     * @return int
     */
    public static function getTotalImprestExpense($department, $finYear)
    {
        $rsp = Builder::table('requeststatusdetails')
            ->select('SUM(amntApproved) as tto')
            ->where('status', '<>', 2)
            ->andWhere('status', '<>', 3)
            ->andWhere('departmentId', $department)
            ->andWhere('financialYear', $finYear)
            ->get();

        $rsp = json_decode($rsp, true);
        if ($rsp['code'] == 200) {
            return $rsp['response'][0]['tto'];
        }
        return null;
    }

    /**
     *Calculate the balance budget balance on imprest account
     * @param $department
     * @return int
     */
    private static function calcImprestBal($department)
    {
        $accnt = '5000';
        //first get the total amount (for both fully approved, pending and still not yet fully approved)

        $budget = LedgerModel::getFinancialYrBudget($department, $accnt)['response'][0]['runningBal'];

        return $budget - self::getTotalImprestExpense($department, FinanceModel::getFinYear());
    }

    protected static function getImprestId()
    {
        $rs = Builder::table('imprestrequests')
            ->select('ImprestId')
            ->orderBy('dateRecCreated')
            ->get(1);
        $rs = json_decode($rs, true);
        if ($rs['code'] == 300) {
            return "IMPR001";
        }

        if ($rs['code'] == 200) {
            $temp = $rs['response'][0]['ImprestId'];
            $last = explode('IMPR', $temp)[1];

            $n = $last + 1;
            return $n < 100 ? 'IMPR0' . $n : 'IMPR' . $n;
        }
        return 'IMPR001'; //hope we neva reach here
    }

    protected static function hasExceededBudget($requestedAmount, $department)
    {
        //verify amount:budget remaining if it allow
        $bal = self::calcImprestBal($department);

        if ($bal < 1) { //if already the balance is less than 0 ksh
            return true;
        }

        if (($bal - $requestedAmount) < 1) {
            return true;
        }
        return false;
    }

    /**
     * Check if a user has more than one un-surrendered imprest requests
     * @param $user
     * @return bool|null
     */
    protected static function has2PendingSurrenders($user)
    {
        $rsp = json_decode(Builder::rawSelect(/** @lang text */
            "SELECT COUNT(requeststatusdetails.id) AS ct FROM requeststatusdetails 
            left JOIN imprestsurrender ON requeststatusdetails.ImprestId=imprestsurrender.refId
             WHERE requeststatusdetails.requestLevel =5 AND requeststatusdetails.status=4 AND
              requeststatusdetails.applicant='$user' AND 
              date(requeststatusdetails.surrenderDate)>CURRENT_DATE()"), //date for sanity check
            true);
        if ($rsp['code'] == 200) {

            if ($rsp['response'][0]['ct'] > 1) {
                return true;
            }
            return false;
        }
        //an error occurred
        return null;
    }

    protected static function saveRecommendation($requestId, $filearray)
    {
        $resp['status'] = 'error';
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png",'pdf'=>'application/pdf');

        $filename = $filearray["name"];
        $filesize = $filearray["size"];

        $ext = pathinfo($filename, PATHINFO_EXTENSION); //get the file extension

        //check if the file extension matches the allowed
        if (!array_key_exists($ext, $allowed)) {
            $resp['response'] = "Image format not allowed. Only .jpg, .jpeg and .png files are allowed";
            return $resp;
        }
        //ensure the file is less than 5 mb
        if ($filesize > (5 * 1024 * 1024)) {
            $resp['response'] = "Image size should not be greater than 5MB";
            return $resp;
        }

        move_uploaded_file($filearray["tmp_name"], "./storage/imprest/recommendations/" . $requestId . '.' . $ext);

        return null;
    }

    protected static function saveReceipt($refId, $filearray)
    {
        $resp['status'] = 'error';

        $allowed = array("pdf" => "application/pdf");

        $filename = $filearray["name"];
        $filesize = $filearray["size"];

        $ext = pathinfo($filename, PATHINFO_EXTENSION); //get the file extension

        //check if the file extension matches the allowed
        if (!array_key_exists($ext, $allowed)) {
            $resp['response'] = "File format not allowed. Only .pdf files are allowed";
            return $resp;
        }
        //ensure the file is less than 5 mb
        if ($filesize > (5 * 1024 * 1024)) {
            $resp['response'] = "File size should not be greater than 5MB";
            return $resp;
        }

        move_uploaded_file($filearray["tmp_name"], "./storage/imprest/receipts/" . $refId . '.' . $ext);

        return null;
    }


    /**
     * Fetch all request to be approved by designate X
     * @param $user :the designate screen ID
     * @return mixed
     */
    protected static function fetchWarrantRequestToApprove($user)
    {
        $resp = ["status" => 'success', "response" => null, "code" => 300];
        $data = [];

        //select addressed to the current user and not rejected or cancelled
        $rsp = Builder::table('requeststatusdetails')
            ->where('designee', $user)
            ->andWhere('status', '<>', 2)//not rejected
            ->andWhere('status', '<>', 3)//not cancelled
            ->get();
        $rsp = json_decode($rsp, true);
        if ($rsp['code'] == 200) {
            foreach ($rsp['response'] as $request) {
                //get the applicants details and merge with each request
                array_push($data, array_merge($request,
                        UsersModel::getUserDetails($request['applicant'])['response'][0])
                );
            }
            $resp['response'] = $data;
            $resp['code'] = 200;
            return Linter::jsonize($resp);
        }

        return Linter::jsonize($resp);
    }

    protected static function fetchSurrenderRequestToApprove($user)
    {
        $resp = ["status" => 'success', "response" => null, "code" => 300];
        $data = [];

        //select addressed to the current user and not rejected or cancelled
        $rsp = Builder::table('surrenderstatusdetails')
            ->where('designee', $user)
            ->andWhere('status', '<>', 2)//not rejected
            ->andWhere('status', '<>', 3)//not cancelled
            ->get();
        $rsp = json_decode($rsp, true);
        if ($rsp['code'] == 200) {
            foreach ($rsp['response'] as $request) {
                //get the applicants details and merge with each request
                array_push($data, array_merge($request,
                        UsersModel::getUserDetails($request['applicant'])['response'][0])
                );
            }
            $resp['response'] = $data;
            $resp['code'] = 200;
            return Linter::jsonize($resp);
        }

        return Linter::jsonize($resp);
    }


    protected static function getImprestRequestDetails($imprestId)
    {
        $resp = ["status" => 'success', "response" => null, "code" => 300];
        $data = [];

        $rsp = Builder::table('requeststatusdetails')
            ->where('ImprestId', $imprestId)
            ->get();

        $rsp = json_decode($rsp, true);
        if ($rsp['code'] == 200) {
            $request = $rsp['response'][0];

            $budget = LedgerModel::getFinancialYrBudget($request['departmentId'], 5000)['response'][0]['runningBal'];

            $expense = self::getTotalImprestExpense($request['departmentId'], FinanceModel::getFinYear()); //all imprest expenses

            //get the applicants details and merger with each request
            array_push($data, array_merge($request,
                    UsersModel::getUserDetails($request['applicant'])['response'][0],
                    ['budget' => $budget, "expense" => $expense])
            );


            $resp['response'] = $data;
            $resp['code'] = 200;
            return Linter::jsonize($resp);
        }
        return Linter::jsonize($resp);
    }

    /**
     * Fetch the details of imprest surrender
     * @param $imprestId :the associated imprest warrant ID
     * @param $refId :the imprest surrender application reference ID
     * @return mixed
     */
    protected static function getImprestSurrenderDetails($imprestId, $refId)
    {

        $resp = ["status" => 'success', "response" => null, "code" => 300];
        $data = [];

        $rsp = Builder::table('surrenderstatusdetails')
            ->where('refId', $refId)
            ->get();
        $rsp = json_decode($rsp, true);
        if ($rsp['code'] == 200) {

            $extra=json_encode(self::getImprestRequestDetails($imprestId));

            $extra=json_decode($extra,true);

            $request = $rsp['response'][0];

            //get the applicants details and merger with each request
            array_push($data, array_merge($request,["extra"=>$extra['response'][0]]));
            $resp['response'] = $data;
            $resp['code'] = 200;
            return Linter::jsonize($resp);
        }
        return Linter::jsonize($resp);

    }

    protected static function processRequisitionRequest()
    {

        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['imprestId']) || empty(Linter::sanitize($_POST['imprestId']))) {
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['status'])) {
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['amount']) || empty(Linter::sanitize($_POST['amount']))) {
            return Linter::jsonize($resp);
        }
        //all set

        $temp = self::getWarrantApprovingStaff(
            Linter::sanitize($_POST['dept']),
            Linter::sanitize($_POST['level']),
            Linter::sanitize($_POST['parentDepartment']),
            Linter::sanitize($_POST['role']),
            Linter::sanitize($_POST['amount'])
        );

        if ($temp['status'] != 'success' || $temp['code'] != 200) {
            $resp['response'] = 'Sorry. System is experiencing unexpected errors. Please try again later';
            return Linter::jsonize($resp);
        }

        $approvingStaff = $temp['response'][0]['screenId']; //get the approving staff screen id

        if (Linter::sanitize($_POST['status']) == 0) { //request rejected


            $rs = Builder::table('requestsapprovals')
                ->insert(
                    Linter::sanitize($_POST['imprestId']),
                    $approvingStaff,
                    Linter::sanitize($_POST['amount']),
                    Linter::sanitize($_POST['comment']), '2',
                    Linter::sanitize($_POST['level']),
                    Linter::sanitize(Carbon::now())
                )
                ->into('imprestId', 'screenId', 'amount', 'comments', 'status', 'requestLevel', 'dateRecCreated');
        } else {
            //request approved
            $rs = Builder::table('requestsapprovals')
                ->insert(
                    Linter::sanitize($_POST['imprestId']),
                    $approvingStaff,
                    Linter::sanitize($_POST['amount']),
                    Linter::sanitize($_POST['comment']), '1',
                    Linter::sanitize($_POST['level']),
                    Linter::sanitize(Carbon::now())
                )
                ->into('imprestId', 'screenId', 'amount', 'comments', 'status', 'requestLevel', 'dateRecCreated');

        }

        $rs = json_decode($rs, true);

        if ($rs['status'] == 'success') {
            $resp = ['status' => 'success', 'response' => 'Request processed successfully'];
            return Linter::jsonize($resp);
        }

        $resp['response'] = 'Request not processed successfully. Please try again.';
        return Linter::jsonize($resp);
    }

    /**
     * Get imprest requests that current user have not surrendered (have already been approved at level 5)
     * @return mixed
     */
    protected static function getImprestToSurrender()
    {

        $user = AppGlobalsModel::currentUserDetails()['response'][0]['screenId'];

        return json_decode(Builder::rawSelect(/** @lang text */
            "SELECT  * FROM   requeststatusdetails WHERE
              requeststatusdetails.requestLevel = 5 
              AND requeststatusdetails.status = 4 
              AND requeststatusdetails.applicant = '$user'
              AND requeststatusdetails.ImprestId NOT IN(
              SELECT  imprestsurrender.imprestId  FROM imprestsurrender)"),
            true);

    }

    public static function processSurrenderRequest()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['refId']) || empty(Linter::sanitize($_POST['refId']))) {
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['status']) || empty(Linter::sanitize($_POST['status']))) {
            return Linter::jsonize($resp);
        }
        $temp = self::getSurrenderApprovingStaff(
            Linter::sanitize($_POST['dept']),
            Linter::sanitize($_POST['level']),
            Linter::sanitize($_POST['parentDepartment']),
            Linter::sanitize($_POST['role']),
            Linter::sanitize($_POST['amount'])
        );

        if ($temp['status'] != 'success' || $temp['code'] != 200) {
            $resp['response'] = 'Sorry. System is experiencing unexpected errors. Please try again later';
            return Linter::jsonize($resp);
        }

        $approvingStaff = $temp['response'][0]['screenId']; //get the approving staff screen id

        if (Linter::sanitize($_POST['status']) == 1) { //request accepted
            $rs=Builder::table('surrenderapprovals')
                ->insert(Linter::sanitize($_POST['refId']),$approvingStaff,
                    Linter::sanitize($_POST['comment']),'1',Linter::sanitize($_POST['level']),Linter::sanitize(Carbon::now()))
                ->into('refId','screenId','comments','status','requestLevel','dateRecCreated');

        }else {
            //request rejected
            $rs=Builder::table('surrenderapprovals')
                ->insert(Linter::sanitize($_POST['refId']),$approvingStaff,
                    Linter::sanitize($_POST['comment']),'2',Linter::sanitize($_POST['level']),Linter::sanitize(Carbon::now()))
                ->into('refId','screenId','comments','status','requestLevel','dateRecCreated');
        }
        $rs = json_decode($rs, true);

        if ($rs['status'] == 'success') {
            $resp = ['status' => 'success', 'response' => 'Request processed successfully'];
            return Linter::jsonize($resp);
        }

        $resp['response'] = 'Request not processed successfully. Please try again.';
        return Linter::jsonize($resp);

    }
}