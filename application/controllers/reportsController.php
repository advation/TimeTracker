<?php

class reportsController extends Staple_Controller
{
    private $authLevel;

    public function _start()
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();
        if($this->authLevel < 500)
        {
            header("location:".$this->_link(array('index','index'))."");
        }
    }

    public function index($year = null, $month = null)
    {
        if($year == null)
        {
            $year = date('Y');
        }

        if($month == null)
        {
            $month = date('m');
        }

        $report = new reportModel($year, $month);
        $this->view->report = $report->getTimesheets();
    }

    public function weekly()
    {
        //Weekly report form
        $form = new weeklyReportForm();

        if($form->wasSubmitted())
        {
            $form->addData($_POST);
            if($form->validate())
            {
                $data = $form->exportFormData();
                $report = new weeklyReportModel();
                $this->view->report = $report->timeWorked($data['account'],$data['year']);

                $account = new userModel();
                $this->view->account = $account->userInfo($data['account']);

                $this->view->year = $data['year'];
            }
            else
            {
                $this->view->form = $form;
            }
        }
        else
        {
            $this->view->form = $form;
        }
    }

    public function unlock()
    {

    }
}