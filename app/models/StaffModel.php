<?php

use Carbon\Carbon;
use Model\Linter;
use mysqlBuilder\Builder;

/**
 * Created by PhpStorm.
 * User: murage
 * Date: 6/8/2018
 * Time: 12:16 PM
 */
class StaffModel
{

    public static function addStaff()
    {

        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];


        //check if the require inputs have been provided

        if (!isset($_POST['fname']) || empty(Linter::sanitize($_POST['fname']))) {
            $resp['response'] = "First name has not been provided.  Please try again";
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['lname']) || empty(Linter::sanitize($_POST['lname']))) {
            $resp['response'] = "last name has not been provided.  Please try again.";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['sname']) || empty(Linter::sanitize($_POST['sname']))) {
            $resp['response'] = "surname has not been provided.  Please try again.";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['staffno']) || empty(Linter::sanitize($_POST['staffno']))) {
            $resp['response'] = "staff number has not been provided.  Please try again.";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['dept']) || empty(Linter::sanitize($_POST['dept']))) {
            $resp['response'] = "Staff department has not been provided.  Please try again.";
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['role']) || empty(Linter::sanitize($_POST['role']))) {
            $resp['response'] = "Staff role has not been provided. Please try again.";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['status'])) {
            $resp['response'] = "Staff account status has not been provided. Please try again.";
            return Linter::jsonize($resp);
        }
        if (!isset($_FILES["sign"]) || $_FILES["sign"]["error"] != 0) {

            $resp['response'] = "Signature could not be uploaded successfully. Please try again.";
            return Linter::jsonize($resp);
        }

        //if all is set
        if (Linter::sanitize($_POST['role']) == 2) {
            if (self::hasHod(Linter::sanitize($_POST['dept']))) {
                $resp['response'] = "Department already has active HOD";
                return Linter::jsonize($resp);
            }
        }

        $screeId = TokensModel::generateStaffId(); //generate staff unique screen id

        //upload the signature first
        $result = self::uploadSignature($_FILES['sign'], $screeId);
        if ($result != null) {
            return Linter::jsonize($result);
        }

        $staffId = Linter::sanitize($_POST['staffno']);

        $resp = Builder::table('staff')
            ->insert(
                Linter::sanitize($screeId), $staffId,
                Linter::sanitize($_POST['fname']), Linter::sanitize($_POST['lname']),
                Linter::sanitize($_POST['sname']), Linter::sanitize($_POST['dept']),
                Linter::sanitize($_POST['role']), Linter::sanitize($_POST['status']),
                Carbon::now()
            )
            ->into('screenId', 'staffnumber', 'firstname', 'lastname', 'surname', 'departmentId',
                'roleId', 'active', 'dateRecCreated');
        $res = json_decode($resp, true);
        if ($res['status'] == 'success') {

            $username = str_replace('/', '', $staffId); //strip slashes

            $cv = UsersModel::configAuth($screeId, $staffId, $username, 2, Linter::sanitize($_POST['mobile']));

            if ($cv['status'] == 'success') { //user auth added successfully
                $resp = ['status' => 'success',
                    'response' => 'Staff member added successfully. Credentials:
                     username=' . $staffId . ' password=' . $username];

                return Linter::jsonize($resp);
            } else {
                $resp = ['status' => 'error', 'response' => 'Request not processed successfully. Please contact the webmaster'];

                return Linter::jsonize($resp);
            }
        }
        //else an error occured
        $resp = ['status' => 'error',
            'response' => 'Sorry! Staff member could not be added. Please contact the webmaster'];

        return Linter::jsonize($resp);
    }

    /**
     * Check if department has a HOD
     * @param $department
     * @return bool
     */
    private static function hasHod($department)
    {
        $rs = Builder::table('staffdetails')
            ->select('count(screenId) as ct')
            ->where('roleId', 2)
            ->andWhere('active', 1)
            ->andWhere('departmentId', $department)
            ->get();
        $rs = json_decode($rs, true);

        if ($rs['status'] == 'success' && $rs['code'] == 200) {


            if ($rs['response'][0]['ct'] > 0) {
                return true;
            }
        }
        return false;
    }

    private static function uploadSignature($filearray, $screenId)
    {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");

        $filename = $filearray["name"];
        $filesize = $filearray["size"];

        $ext = pathinfo($filename, PATHINFO_EXTENSION); //get the file extension

        //check if the file extension matches the allowed
        if (!array_key_exists($ext, $allowed)) {
            return $resp['response'] = "Image format not allowed. Only .jpg, .jpeg and .png files are allowed";
        }
        //ensure the file is less than 5 mb
        if ($filesize > (5 * 1024 * 1024)) {
            return $resp['response'] = "Image size should not be greater than 5MB";
        }

        move_uploaded_file($filearray["tmp_name"], "./storage/signatures/" . $screenId . '.' . $ext);

        return null;
    }

    public static function getAll()
    {
        return json_decode(
            Builder::table('staffdetails')
                ->select("concat(firstname,' ',lastname,' ',surname) as name",
                    'staffnumber', 'screenId', 'department', 'role')
                ->get()
            , true);
    }

    public static function fetchDetails()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];

        if (!isset($_POST['id'])) {
            return json_encode($resp);
        }
        $id = Linter::sanitize($_POST['id']);

        return
            Builder::table('staff')
                ->where('screenId', $id)
                ->get();
    }

    public static function updateDetails()
    {
        $resp = ['status' => 'error', 'response' => "Error in your request. Sorry please try again."];


        //check if the require inputs have been provided

        if (!isset($_POST['fname']) || empty(Linter::sanitize($_POST['fname']))) {
            $resp['response'] = "First name has not been provided.  Please try again";
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['lname']) || empty(Linter::sanitize($_POST['lname']))) {
            $resp['response'] = "last name has not been provided.  Please try again.";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['sname']) || empty(Linter::sanitize($_POST['sname']))) {
            $resp['response'] = "surname has not been provided.  Please try again.";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['staffno']) || empty(Linter::sanitize($_POST['staffno']))) {
            $resp['response'] = "staff number has not been provided.  Please try again.";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['dept']) || empty(Linter::sanitize($_POST['dept']))) {
            $resp['response'] = "Staff department has not been provided.  Please try again.";
            return Linter::jsonize($resp);
        }
        if (!isset($_POST['role']) || empty(Linter::sanitize($_POST['role']))) {
            $resp['response'] = "Staff role has not been provided. Please try again.";
            return Linter::jsonize($resp);
        }

        if (!isset($_POST['status'])) {
            $resp['response'] = "Staff account status has not been provided. Please try again.";
            return Linter::jsonize($resp);
        }

        //if all is set
        if (Linter::sanitize($_POST['role']) == 2) {
            if (self::hasHod(Linter::sanitize($_POST['dept']))) {
                $resp['response'] = "Department already has active HOD";
                return Linter::jsonize($resp);
            }
        }


        $staffId = Linter::sanitize($_POST['staffno']);

        $resp = Builder::table('staff')
            ->where('screenId',Linter::sanitize($_POST['screenId']))
            ->update(['staffnumber' => $staffId,
                    'firstname' => Linter::sanitize($_POST['fname']),
                    'lastname' => Linter::sanitize($_POST['lname']),
                    'surname' => Linter::sanitize($_POST['sname']),
                    'departmentId' => Linter::sanitize($_POST['dept']),
                    'roleId' => Linter::sanitize($_POST['role']),
                    'active' => Linter::sanitize($_POST['status']),
                    'dateRecUpdated' => Carbon::now()]
            );

        $res = json_decode($resp, true);

        return $res;
    }


}