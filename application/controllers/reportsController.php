<?php

class reportsController extends Staple_Controller
{
    private $authLevel;
    private $uid;

    public function _start()
    {
        $this->_setLayout('main');
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
        if ($year == null)
        {
            $date = new DateTime();

            if($date->format('d') >= 26)
            {
                $date->modify('+1 month');
            }

            $year = $date->format('Y');
        }

        if ($month == null)
        {
            $date = new DateTime();

            if($date->format('d') >= 26)
            {
                //$date->modify('+1 month');
                $date->modify('+7 days');
            }

            $month = $date->format('m');
        }

        $date = new DateTime();
        $date->setDate($year,$month,26);
        $date->setTime(0,0,0);

        $this->view->year = $date->format('Y');
        $this->view->date = $date->format("F Y");

        $date->modify('+1 year');
        $this->view->nextYear = $date->format('Y');

        $date->modify('-2 year');
        $this->view->previousYear = $date->format('Y');

        $date->modify('+1 year');

        $month = $date->format('m');
        $this->view->month = $month;

        $date->modify('-1 month');
        $this->view->previousMonth = $date->format('m');
        $date->modify('+2 month');
        $this->view->nextMonth = $date->format('m');

        $report = new reportModel($year, $month);
        $this->view->report = $report->getTimesheets();

        $reviewed = new reviewModel();
        $this->view->reviewed = $reviewed->load($year, $month);

        $this->view->accountLevel = $this->authLevel;

        $date = new DateTime();
        if($date->format('d') >= 26)
        {
            $date->modify('+1 month');
        }

        $date->setDate($year, $month, 1);
        $this->view->monthName = $date->format('F');

        $printActiveTimeSheetForm = new printActiveTimeSheetForm();
        $printActiveTimeSheetForm->setAction($this->_link(array("reports",$year,$month)));
        if($printActiveTimeSheetForm->wasSubmitted())
        {
            $printActiveTimeSheetForm->addData($_POST);
            if($printActiveTimeSheetForm->validate())
            {
                $data = $printActiveTimeSheetForm->exportFormData();

                $this->layout->addScriptBlock("
                    window.open('".$this->_link(array("reports","printpreview",$year,$month,$data['account']))."');
                    ");
                $this->view->printTimeSheetForm = $printActiveTimeSheetForm;
            }
            else
            {
                $this->view->printTimeSheetForm = $printActiveTimeSheetForm;
            }
        }
        else
        {
            $this->view->printTimeSheetForm = $printActiveTimeSheetForm;
        }
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
            $date = new DateTime();

            if($date->format('d') >= 26)
            {
                $date->modify('+4 weeks');
            }

            $timesheets = new reportModel($date->format('Y'), $date->format('m'));

            $this->view->accounts = $timesheets;

            $this->view->dateTitle = $date->format('F')." ".$date->format('Y');
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

    public function printpreview($year,$month,$uid)
    {
        $this->_setLayout('print');

        $user = new userModel();
        $account = $user->userInfo($uid);

        $this->view->firstName = $account['firstName'];
        $this->view->lastName = $account['lastName'];
        $this->view->batchId = $account['batchId'];
        $this->view->year = $year;
        $this->view->month = date('F',$month);

        $timesheet = new timesheetModel($year, $month,$uid);
        $this->view->timesheet = $timesheet;
    }

    public function payperiod($year = null, $month = null)
    {
        if ($year == null) {
            $year = date('Y');
        }

        if ($month == null) {
            $month = date('m');
        }

        $date = new DateTime();
        $date->setDate($year,$month,26);
        $date->setTime(0,0,0);

        $year = $date->format('Y');

        $this->view->year = $year;

        $nextYear = $date->modify('+1 year')->format('Y');
        $this->view->nextYear = $nextYear;

        $previousYear = $date->modify('-2 year')->format('Y');
        $this->view->previousYear = $previousYear;

        $month = $date->format('m');
        $this->view->month = $month;

        $nextMonth = $date->modify('+1 month')->format('m');
        $this->view->nextMonth = $nextMonth;

        $previousMonth = $date->modify('-2 month')->format('m');
        $this->view->previousMonth = $previousMonth;

        $date->setDate($year,$month,26);
        $date->setTime(0,0,0);

        $newDate = new DateTime();
        switch($month)
        {
            case 1:
                $newDate->setDate($previousYear,$previousMonth,26);
                break;
            default:
                $newDate->setDate($year,$previousMonth,26);
        }

        $newDate->setTime(0,0,0);

        $date2 = new DateTime();
        $date2->setDate($year,$month,25);
        $date2->setTime(24,00,00);

        $interval = date_diff($newDate,$date2);

        $span = $interval->days;
        $this->view->span = $span;

        $this->view->date = $date->format("F Y");

        $date = new dateTime();
        $date->setDate($year, $month, 25);
        $this->view->currentDate = $date->format('Y-m-d');
        $this->view->previousDate = $date->modify('-1 month +1 day')->format('Y-m-d');

        $reports = new reportModel($year, $month);
        $this->view->report = $reports->payPeriodTotals($year, $month);

        $requests = $reports->timeOffRequestsForPayPeriod($year, $month);
        $this->view->requests = $requests;
    }

    public function payperiodprint($year,$month)
    {
        $this->_setLayout('print');
        $date = new DateTime();
        $date->setDate($year,$month,26);
        $date->setTime(0,0,0);

        $year = $date->format('Y');

        $this->view->year = $year;

        $nextYear = $date->modify('+1 year')->format('Y');
        $this->view->nextYear = $nextYear;

        $previousYear = $date->modify('-2 year')->format('Y');
        $this->view->previousYear = $previousYear;

        $month = $date->format('m');
        $this->view->month = $month;

        $nextMonth = $date->modify('+1 month')->format('m');
        $this->view->nextMonth = $nextMonth;

        $previousMonth = $date->modify('-2 month')->format('m');
        $this->view->previousMonth = $previousMonth;

        $date->setDate($year,$month,26);
        $date->setTime(0,0,0);

        $newDate = new DateTime();
        switch($month)
        {
            case 1:
                $newDate->setDate($previousYear,$previousMonth,26);
                break;
            default:
                $newDate->setDate($year,$previousMonth,26);
        }

        $newDate->setTime(0,0,0);

        $date2 = new DateTime();
        $date2->setDate($year,$month,25);
        $date2->setTime(24,00,00);

        $interval = date_diff($newDate,$date2);

        $span = $interval->days;
        $this->view->span = $span;

        $this->view->date = $date->format("F Y");

        $date = new dateTime();
        $date->setDate($year, $month, 25);
        $this->view->currentDate = $date->format('Y-m-d');
        $this->view->previousDate = $date->modify('-1 month +1 day')->format('Y-m-d');

        $reports = new reportModel($year, $month);
        $this->view->report = $reports->payPeriodTotals($year, $month);

        $requests = $reports->timeOffRequestsForPayPeriod($year, $month);
        $this->view->requests = $requests;
    }

    public function payroll($year = null, $month =  null)
    {
        if($year == null)
        {
            $year = date('Y');
        }

        if($month == null)
        {
            $month = date('m');
        }

        $this->view->year = $year;

        $date = new DateTime();
        $date->setDate($year,$month,26);
        $date->setTime(0,0,0);

        $this->view->date = $date->format("F Y");

        $date->modify('+1 year');
        $this->view->nextYear = $date->format('Y');

        $date->modify('-2 year');
        $this->view->previousYear = $date->format('Y');

        $this->view->month = $date->format('m');
        $date->modify('-1 month');
        $this->view->previousMonth = $date->format('m');
        $date->modify('+2 month');
        $this->view->nextMonth = $date->format('m');

        $date2 = new DateTime();
        $date2->setDate($year,$month,25);
        $date2->setTime(24,0,0);

        $interval = date_diff($date,$date2);

        $this->view->span = $interval->days;

        $reports = new reportModel($year, $month);

        $this->view->report = $reports->payroll($year, $month);
        $this->view->startDate = $date->format("F jS Y");
        $days = $interval->days - 1;
        $date->modify("+$days days");
        $this->view->endDate = $date->format("F jS Y");

        $date = new dateTime();
        $date->setDate($year, $month, 25);
        $this->view->currentDate = $date->format('Y-m-d');
        $this->view->previousDate = $date->modify('-1 month +1 day')->format('Y-m-d');

        $codes = new codeModel();
        $this->view->codes = $codes->allCodes();
    }

    public function payrollprint($year = null, $month =  null)
    {
        if($year != null || $month != null) {
            $this->_setLayout('print');
            if ($year == null) {
                $year = date('Y');
            }

            if ($month == null) {
                $month = date('m');
            }

            $this->view->year = $year;

            $date = new DateTime();
            $date->setDate($year, $month, 26);
            $date->setTime(0, 0, 0);
            $this->view->month = $date->format('m');
            $date->modify('-1 month');
            $this->view->previousMonth = $date->format('m');

            $date2 = new DateTime();
            $date2->setDate($year, $month, 25);
            $date2->setTime(24, 0, 0);

            $interval = date_diff($date, $date2);

            $this->view->span = $interval->days;

            $reports = new reportModel($year, $month);
            $this->view->report = $reports->payroll($year, $month);
            $this->view->startDate = $date->format("F jS Y");
            $days = $interval->days - 1;
            $date->modify("+$days days");
            $this->view->endDate = $date->format("F jS Y");

            $codes = new codeModel();
            $this->view->codes = $codes->allCodes();
        }
        else
        {
            header("location:".$this->_link(array('reports','payroll'))."");
        }
    }

    public function inactive($year = null, $month = null)
    {
        if ($year == null)
        {
            $date = new DateTime();

            if($date->format('d') >= 26)
            {
                $date->modify('+1 month');
            }

            $year = $date->format('Y');
        }

        if ($month == null)
        {
            $date = new DateTime();

            if($date->format('d') >= 26)
            {
                $date->modify('+1 month');
            }

            $month = $date->format('m');
        }

        $date = new DateTime();
        $date->setDate($year,$month,26);
        $date->setTime(0,0,0);

        $this->view->year = $date->format('Y');
        $this->view->date = $date->format("F Y");

        $date->modify('+1 year');
        $this->view->nextYear = $date->format('Y');

        $date->modify('-2 year');
        $this->view->previousYear = $date->format('Y');

        $date->modify('+1 year');

        $month = $date->format('m');
        $this->view->month = $month;

        $date->modify('-1 month');
        $this->view->previousMonth = $date->format('m');
        $date->modify('+2 month');
        $this->view->nextMonth = $date->format('m');

        $report = new reportModel($year, $month,1);
        $this->view->report = $report->getTimesheets();

        $this->view->accountLevel = $this->authLevel;

        $date = new DateTime();
        if($date->format('d') >= 26)
        {
            $date->modify('+1 month');
        }

        $date->setDate($year, $month, 1);
        $this->view->monthName = $date->format('F');

        $printInactiveTimeSheetForm = new printInactiveTimeSheetForm();
        $printInactiveTimeSheetForm->setAction($this->_link(array("reports","inactive",$year,$month)));
        if($printInactiveTimeSheetForm->wasSubmitted())
        {
            $printInactiveTimeSheetForm->addData($_POST);
            if($printInactiveTimeSheetForm->validate())
            {
                $data = $printInactiveTimeSheetForm->exportFormData();

                $this->layout->addScriptBlock("
                    window.open('".$this->_link(array("reports","inactive","printpreview",$year,$month,$data['account']))."');
                    ");
                $this->view->printTimeSheetForm = $printInactiveTimeSheetForm;
            }
            else
            {
                $this->view->printTimeSheetForm = $printInactiveTimeSheetForm;
            }
        }
        else
        {
            $this->view->printTimeSheetForm = $printInactiveTimeSheetForm;
        }
    }

    public function reviewed($userId = null, $year = null, $month = null)
    {
        if($userId != null || $year != null || $month != null)
        {
            $review = new reviewModel();

            $review->setAccountId($userId);
            $review->setPayPeriodMonth($month);
            $review->setPayPeriodYear($year);

            if($review->save())
            {
                header("location: ".$this->_link(array('reports',$year,$month,$userId))."");
            }
            else
            {
                $this->view->year = $year;
                $this->view->month = $month;
                $this->view->errorMessage = "Entry already marked as reviewed.";
            }
        }
        else
        {
            header("location: ".$this->_link(array('reports',$year,$month,$userId))."");
        }
    }

    public function requests()
    {
        $user = new userModel();
        if($user->getAuthLevel() == 900)
        {
            $form = new changeDateRangeForm();
            $report = new requestReportModel();

            if ($form->wasSubmitted())
            {
                $form->addData($_POST);
                if ($form->validate())
                {
                    $data = $form->exportFormData();
                    $startDate = $data['startDate'];
                    $endDate = $data['endDate'];
                    $this->view->report = $report->dateRangeRequests($startDate, $endDate);
                    $form = new changeDateRangeForm();
                    $this->view->form = $form;
                }
                else
                {
                    $this->view->form = $form;
                    $this->view->formError = 1;
                    $this->view->report = $report->currentPayperiodRequests();
                }
            }
            else
            {
                $this->view->form = $form;
                $this->view->report = $report->currentPayperiodRequests();
            }

            $users = new userModel();
            $this->view->users = $users->listActive();
        }
        else
        {
            header("location: ".$this->_link(array('reports','staffrequests'))."");
        }
    }

    public function staffrequests()
    {
        $report = new requestReportModel();

        $form = new changeDateRangeForm();
        $form->setAction($this->_link(array('reports','staffrequests')));

        if ($form->wasSubmitted())
        {
            $form->addData($_POST);
            if ($form->validate())
            {
                $data = $form->exportFormData();
                $startDate = $data['startDate'];
                $endDate = $data['endDate'];
                $this->view->report = $report->staffRequests($startDate, $endDate);
                $form = new changeDateRangeForm();
                $form->setAction($this->_link(array('reports','staffrequests')));
                $this->view->form = $form;
            }
            else
            {
                $this->view->form = $form;
                $this->view->formError = 1;
                $this->view->report = $report->staffRequests();
            }
        }
        else
        {
            $this->view->form = $form;
            $this->view->report = $report->staffRequests();
        }
        $users = new userModel();
        $this->view->users = $users->assignedUsers();
    }
}