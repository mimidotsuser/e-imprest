<?php


Router::get('','HomeController@index');
Router::get('login','HomeController@index');
Router::get('login/two-step-verification-view','HomeController@twowayauthview');

Router::post('login','HomeController@authenticate');

Router::post('/login/two-step-verification','HomeController@twowayauth');


Router::get('admin','AdminController@index');
Router::get('admin/dashboard','AdminController@index');
Router::get('admin/add_staff','AdminController@vaddstaff');
Router::get('admin/view_all','AdminController@vallstaff');
Router::get('admin/reports','AdminController@staffreport');
Router::get('admin/add_department','AdminController@vadddept');
Router::get('admin/account','AdminController@vpassword');

Router::post('admin/account','AdminController@password');

Router::post('admin/add_staff','AdminController@addStaff');
Router::post('admin/add_department','AdminController@addDept');
Router::post('admin/update_department','AdminController@updateDept');

Router::post('admin/view_all','AdminController@updateStaff');




Router::get('staff','StaffController@index');
Router::get('staff/home','StaffController@index');
Router::get('staff/imprest/apply','StaffController@vimprestA');
Router::get('staff/imprest/surrender','StaffController@vimprestS');
Router::get('staff/imprest/approve','StaffController@vimprestP');
Router::get('staff/budget','StaffController@vbudget');
Router::get('staff/ledger','StaffController@vledger');

Router::get('staff/chartofaccnts','StaffController@vcoa');
Router::get('staff/financial_year','StaffController@vfyear');

Router::get('staff/settings/account','StaffController@vpassword');

Router::post('staff/chartofaccnts','StaffController@addCoa');
Router::post('staff/financial_year','StaffController@addfyear');
Router::post('staff/budget','StaffController@budget');
Router::post('staff/imprest/apply','StaffController@imprestA');
Router::post('staff/imprest/approve/view','StaffController@imprestrequestview');//view request details
Router::post('staff/imprest/approve','StaffController@imprestApproval'); //request approval

Router::post('staff/imprest/surrender/apply','StaffController@surrenderApply'); //surrender form view
Router::post('staff/imprest/surrender','StaffController@surrender'); //apply surrender

Router::post('staff/surrender/approve/view','StaffController@surrenderrequestview'); //apply surrender
Router::post('staff/surrender/approve','StaffController@surrenderrequestapproval'); //apply surrender



Router::get('staff/imprest/reports','ImprestSubController@vreports');
Router::post('staff/imprest/reports/timeline','ImprestSubController@timeline');

Router::get('staff/imprest/reports/coa','ImprestSubController@coareport');
Router::get('staff/imprest/reports/budget','ImprestSubController@budgetreport');
Router::get('staff/imprest/reports/ledger','ImprestSubController@ledgerreport');

Router::post('staff/settings/account','StaffController@password');



//api endpoints
Router::post('admin/staff/data','ApiController@staffDetails');
Router::post('admin/department/data','ApiController@departmentDetails');

?>