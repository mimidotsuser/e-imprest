<?php
/**
 * Created by PhpStorm.
 * User: mimidots
 * Date: 6/18/2018
 * Time: 6:32 PM
 */

class ImprestSubController
{


    /**
     * ImprestSubController constructor.
     */
    public function __construct()
    {
        Auth::middleware(['staff']);
    }

    public function vreports()
    {
        $data=['mywarrants'=>ImprestManager::fetchAllMyImprestWarrantApplications(),
            'mysurrenders'=>ImprestManager::fetchAllMyImprestSurrenderApplications()
        ];
        view('imprestreports',$data);
    }

    public function timeline()
    {
        $data=['data'=>ImprestManager::fetchTimelineData()];
        view('timeline',$data);
    }

    public function coareport()
    {
        PrintOut::genChartOfAccntsReport();
    }

    public function budgetreport()
    {
        PrintOut::genBudgetReport();
    }

    public function ledgerreport()
    {
        LedgerPrintOut::genLedger();
    }
}