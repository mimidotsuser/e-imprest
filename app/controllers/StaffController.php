<?php
/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/8/2018
 * Time: 7:56 AM
 */

class StaffController
{

    /**
     * StaffController constructor.
     */
    public function __construct()
    {
        Auth::middleware(['staff']);
    }

    public function index()
    {
        $data=['warrant'=>ReportsModel::imprestWarrantApplicationsCount(),
            'surrender'=>ReportsModel::imprestSurrenderApplicationsCount(),
            'warrantsToApprove'=>ReportsModel::warrantApprovalsCount(),
            'surrendersToApprove'=>ReportsModel::surrenderApprovalsCount()
        ];
        view('home',$data);
    }

    public function vimprestA()
    {
        view('imprestapply');
    }

    public function vimprestS()
    {
        $data=['all'=>ImprestManager::fetchForSurrender()];
        view('imprestsurrender',$data);
    }

    public function surrenderApply()
    {
        $data=['request'=>ImprestManager::viewImprestRequestDetails()];
        view('surrenderapply',$data);
    }

    public function vimprestP()
    {
        $data = ["warrantrequests" => ImprestManager::warrantApplicationsForApproval(),
            'surrenderrequests'=>ImprestManager::surrenderApplicationsForApproval()];
        view('approveimprest', $data);
    }

    public function imprestrequestview()
    {
        $data = ["request" => ImprestManager::viewImprestRequestDetails()];
        view('imprestrequestview', $data);
    }

    public function surrenderrequestview()
    {
        $data=['request'=>ImprestManager::viewImprestSurrenderDetails()];
        view('surrenderrequestview',$data);
    }


    public function vbudget()
    {
        $data = [
            "budget" => FinanceModel::loadBudgetEntries(),
            "fin" => FinanceModel::getFinancialYears(),
            'dept' => DepartmentsModel::fetchAll()
        ];

        view('budget', $data);
    }

    public function vledger()
    {
        $data=["data"=>LedgerModel::ledgerAccntDetails(),
            "fin" => FinanceModel::getFinancialYears()
        ];
        view('ledger',$data);
    }
    public function vcoa()
    {
        $data = [
            'accnttype' => CoaModel::getAcctTypes(),
            'accnts' => CoaModel::getAccnts(),
            'depart'=>DepartmentsModel::fetchAll()
        ];
        view('coa', $data);
    }

    public function vfyear()
    {
        $data = ['data' => FinanceModel::getFinancialYears()];
        view('financialyear', $data);
    }

    public function vpassword()
    {
        view('accnt');
    }


    public function addCoa()
    {
        $data = [
            'notify' => CoaModel::add(),
            'accnttype' => CoaModel::getAcctTypes(),
            'accnts' => CoaModel::getAccnts(),
            'depart'=>DepartmentsModel::fetchAll()
        ];
        view('coa', $data);
    }

    public function budget()
    {
        $data = [
            "notify" => FinanceModel::budgetEntry(),
            "budget" => FinanceModel::loadBudgetEntries(),
            "fin" => FinanceModel::getFinancialYears(),
            'dept' => DepartmentsModel::fetchAll()
        ];

        view('budget', $data);
    }

    public function addfyear()
    {

        $data = [
            'notify' => FinanceModel::manage(),
            'data' => FinanceModel::getFinancialYears()
        ];
        view('financialyear', $data);
    }

    /**
     *imprest applications
     */
    public function imprestA()
    {
        $data = ['notify' => ImprestManager::applyRequest()];
        view('imprestapply', $data);
    }

    //apply surrender
    public function surrender()
    {
        $data=[
            'notify'=>ImprestManager::applySurrender(),
            'all'=>ImprestManager::fetchForSurrender()];
        view('imprestsurrender',$data);

    }

    public function imprestApproval()
    {

        $data=['notify'=>ImprestManager::imprestWarrantApproval(),
            "warrantrequests" => ImprestManager::warrantApplicationsForApproval(),
         'surrenderrequests'=>ImprestManager::surrenderApplicationsForApproval()];
        view('approveimprest',$data);
    }

    public function surrenderrequestapproval()
    {
        $data=['notify'=>ImprestManager::imprestSurrenderApproval(),
            "warrantrequests" => ImprestManager::warrantApplicationsForApproval(),
            'surrenderrequests'=>ImprestManager::surrenderApplicationsForApproval()];
        view('approveimprest',$data);
    }

    public function password()
    {
        $data=["notify"=>UsersModel::changePassword()];
        view('accnt',$data);
    }

}