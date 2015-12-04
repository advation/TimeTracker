<?php

class reportsController extends Staple_Controller
{
    private $authLevel;

    public function _start()
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();
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
            $rangeForm = new rangeUnlockForm();

            if ($rangeForm->wasSubmitted()) {
                $rangeForm->addData($_POST);
                if ($rangeForm->validate()) {
                    $data = $rangeForm->exportFormData();
                    $unlock = new unlockModel();
                    $unlock->setStartTime($data['startDate']);
                    $unlock->setEndTime($data['endDate']);
                    $unlock->setUserId($data['account']);
                    $unlock->save();
                    $this->view->rangeForm = new rangeUnlockForm();
                } else {
                    $this->view->rangeForm = $rangeForm;
                }
            } else {
                $this->view->rangeForm = $rangeForm;
            }

            $singleForm = new singleUnlockForm();
            if ($singleForm->wasSubmitted()) {
                $singleForm->addData($_POST);
                if ($singleForm->validate()) {
                    $data = $singleForm->exportFormData();
                } else {
                    $this->view->singleForm = $singleForm;
                }
            } else {
                $this->view->singleForm = $singleForm;
            }

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

            if ($unlock->unlock($id)) {
                $this->view->message = "<i class='fa fa-check'></i> Time entry unlocked.";
            } else {
                $this->view->message = "<i class='fa fa-close'></i> ERROR: Unable to unlock your own time entries.";
            }
        }
    }
}