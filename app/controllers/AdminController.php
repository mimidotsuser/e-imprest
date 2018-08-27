<?php
/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/8/2018
 * Time: 7:56 AM
 */

class AdminController
{


    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        Auth::middleware(['admin']);
    }

    public function index()
    {
        $data=['dCount'=>ReportsModel::departCount(),
            'sCount'=>ReportsModel::staffCount(),
            'sACount'=>ReportsModel::staffAccntActiveCount()
        ];
        view('admin/dashboard',$data);
    }

    public function vaddstaff()
    {
        $data=[
            "depts"=>DepartmentsModel::fetchAll(),
            "userrole"=>DepartmentsModel::userRoles()];
        view('admin/addstaff',$data);
    }

    public function vallstaff()
    {
        $data=[
            'data'=>StaffModel::getAll(),
        "depts"=>DepartmentsModel::fetchAll(),
        "userrole"=>DepartmentsModel::userRoles()
        ];
        view('admin/allstaff',$data);
    }

    public function staffreport()
    {
        view('admin/reports');
    }

    public function vadddept()
    {
        $data=['data'=>DepartmentsModel::fetchAll()];

        view('admin/adddept',$data);
    }

    public function vpassword()
    {
        view('admin/accnt');
    }


    public function addStaff()
    {
        $data=[
            'notify'=>StaffModel::addStaff(),
            "depts"=>DepartmentsModel::fetchAll(),
            "userrole"=>DepartmentsModel::userRoles()
        ];
        view('admin/addstaff',$data);
    }

    public function addDept()
    {

        $data=[
            'notify'=>DepartmentsModel::add(),
            'data'=>DepartmentsModel::fetchAll()];

        view('admin/adddept',$data);
    }

    public function password()
    {
        $data=['notify'=>UsersModel::changePassword()];
        view('admin/accnt',$data);
    }

    public static function updateDept()
    {
        $data=[
            'notify'=>DepartmentsModel::update(),
            'data'=>DepartmentsModel::fetchAll()];

        view('admin/adddept',$data);
    }

    public function updateStaff()
    {
        $data=[
            'notify'=>StaffModel::updateDetails(),
            'data'=>StaffModel::getAll(),
            "depts"=>DepartmentsModel::fetchAll(),
            "userrole"=>DepartmentsModel::userRoles()
        ];
        view('admin/allstaff',$data);

    }


}
