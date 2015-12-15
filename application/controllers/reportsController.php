<?php

class reportsController extends Staple_Controller
{
    private $authLevel;
    private $uid;

    public function _start()
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();
        $user = new userModel();
        $this->uid = $user->getId();
        if ($this->authLevel < 500) {
            header("location:" . $this->_link(array('index', 'index')) . "");
        }
    }

    public function index($year = null, $month = null)
    {
        if ($year == null) {
            $year = date('Y');
        }

        if ($month == null) {
            $month = date('m');
        }

        $report = new reportModel($year, $month);
        $this->view->report = $report->getTimesheets();

        $timesheet = new timesheetModel($year, $month);
        $this->view->nextMonth = $timesheet->getNextMonth();
        $this->view->previousMonth = $timesheet->getPreviousMonth();
        $this->view->year = $timesheet->getCurrentYear();
        $yearForm = new changeYearForm();
        $yearForm->setAction($this->_link(array('reports','changeyear')));
        $this->view->yearForm = $yearForm;

        $this->view->accountLevel = $this->authLevel;

        $date = new DateTime();
        $date->setDate($year, $month, 1);
        $this->view->month = $date->format('F');
    }

    public function changeyear()
    {
        $form = new changeYearForm();
        if($form->wasSubmitted())
        {
            $form->addData($_POST);
            if($form->validate())
            {
                $data = $form->exportFormData();
                header("location: ".$this->_link(array('reports',$data['year']))."");
            }
            else
            {
                header("location: ".$this->_link(array('reports'))."");
            }
        }
        else
        {
            header("location: ".$this->_link(array('reports'))."");
        }
    }

    public function weekly()
    {
        //Weekly report form
        $form = new weeklyReportForm();

        if ($form->wasSubmitted()) {
            $form->addData($_POST);
            if ($form->validate()) {
                $data = $form->exportFormData();
                $report = new weeklyReportModel();
                $this->view->report = $report->timeWorked($data['account'], $data['year']);

                $account = new userModel();
                $this->view->account = $account->userInfo($data['account']);

                $this->view->year = $data['year'];
            } else {
                $this->view->form = $form;
            }
        } else {
            $this->view->form = $form;
        }
    }

    public function unlock()
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();
        if ($this->authLevel < 900)
        {
            header("location:" . $this->_link(array('index', 'index')) . "");
        }
        else
        {

            $year = date('Y');
            $month = date('m');

            $timesheets = new reportModel($year, $month);

            $this->view->accounts = $timesheets;
        }
    }

    public function unlockid($id)
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();

        if ($this->authLevel < 900)
        {
            header("location:" . $this->_link(array('index', 'index')) . "");
        }
        else
        {
            $unlock = new unlockModel();

            if ($unlock->unlock($id))
            {
                $this->view->message = "<i class='fa fa-check'></i> Time entry unlocked.";
            }
            else
            {
                $this->view->message = "<i class='fa fa-close'></i> ERROR: Unable to unlock your own time entries.";
            }
        }
    }
}