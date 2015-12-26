<?php
class timesheetController extends Staple_Controller
{
    private $userId;
    private $accountLevel;

    public function _start()
    {
        $this->_setLayout('main');
        $auth = Staple_Auth::get();
        $user = new userModel();
        $user->userInfo($auth->getAuthId());
        $this->userId = $user->getId();
        $this->accountLevel = $user->getAuthLevel();
    }

    public function index($year = null, $month = null)
    {
        //Typecast variables
        $month = (int) $month;
        $year = (int) $year;

        //Build new insert form
        $form = new insertTimeForm();

        //Check for form submission
        if($form->wasSubmitted())
        {
            //Add submitted data to the form
            $form->addData($_POST);

            //Check form validation
            if($form->validate())
            {
                //Export form data into an array
                $data = $form->exportFormData();

                //Check if dates are within the current pay period.
                $date = new DateTime();

                if($date->format('d') > 25)
                {
                    $date->modify('+1 month');
                }
                $maxDate = $date->setDate($date->format('Y'),$date->format('m'),25)->setTime(23,59,59)->getTimestamp();
                $minDate = $date->modify('-1 month +1 day')->setTime(0,0,0)->getTimestamp();
                $userDate = strtotime($data['date']);

                //Date is within pay period
                if($userDate >= $minDate && $userDate <= $maxDate)
                {
                    //Create a new entry object and set properties
                    $entry = new timeEntryModel();
                    $entry->setDate($data['date']);
                    $entry->setInTime($data['inTime']);
                    $entry->setOutTime($data['outTime']);
                    $entry->setLessTime($data['lessTime']);
                    $entry->setCodeId($data['code']);

                    //Save entry data to table.
                    if($entry->save())
                    {
                        //Return a new time form with success message
                        $form = new insertTimeForm();
                        $form->successMessage = array("<i class=\"fa fa-check\"></i> Entry saved for ".$data['date']."");
                        $this->view->insertTimeForm = $form;
                    }
                    else
                    {
                        //Return the same form with a warning message
                        $message = "<i class=\"fa fa-warning\"></i> Cannot insert overlapping time entries. Please add a new entry or edit an already existing one.";
                        $form->errorMessage = array($message);
                        $this->view->insertTimeForm = $form;
                    }
                }
                else
                {
                    //Return the same form with error message.
                    $form->errorMessage = array("<i class='fa fa-warning'></i> You may only submit time for the current date period.");
                    $this->view->insertTimeForm = $form;
                }
            }
            else
            {
                //Return form with invalid data.
                $this->view->insertTimeForm = $form;
            }
        }
        else
        {
            //Return form
            $this->view->insertTimeForm = $form;
        }

        //Set year and month variables if undefined.
        if($year == null)
        {
            $date = new DateTime();
            $date->setTime(0,0,0);
            $year = $date->format('Y');
        }

        if($month == null)
        {
            $date = new DateTime();
            $date->setTime(0,0,0);
            if($date->format("j") >= 26)
            {
                $month = $date->modify('+1 month')->format('m');
            }
            else
            {
                $month = $date->format('m');
            }
        }

        //Load timesheet for user.
        $timesheet = new timesheetModel($year,$month);

        //Pass timesheet object to view
        $this->view->timesheet = $timesheet;

        //Check for unvalidated entries
        $i = 0;
        foreach($timesheet->getEntries() as $entry)
        {
            if($entry->batchId == $timesheet->getBatch())
            {
                $i++;
            }
        }

        if($i > 0)
        {
            $this->view->needsValidation = true;
        }
        else
        {
            $this->view->needsValidation = false;
        }

        $changeYearForm = new changeYearForm();
        $this->view->changeYearForm = $changeYearForm;
    }

    public function printpreview($id = null, $year = null, $month = null)
    {
        $this->_setLayout('print');

        //Set year and month variables if undefined.
        if($year == null)
        {
            $date = new DateTime();
            $year = $date->format('Y');
        }

        if($month == null)
        {
            $date = new DateTime();
            if($date->format("j") >= 26)
            {
                $month = $date->modify('+1 month')->format('m');
            }
            else
            {
                $month = $date->format('m');
            }
        }

        //Load timesheet for user.
        $timesheet = new timesheetModel($year,$month);

        $user = new userModel();
        $user->userInfo($this->userId);

        $this->view->firstName = $user->getFirstName();
        $this->view->lastName = $user->getLastName();
        $this->view->batchId = $user->getBatchId();

        //Pass timesheet object to view
        if($id == $this->userId)
        {
            $this->view->timesheet = $timesheet;
        }
        else
        {
            header("location: ".$this->_link(array('timesheet'))."");
        }
    }

    public function remove($id = null)
    {
        if($id != null)
        {
            //Confirm entry for user
            $timeEntry = new timeEntryModel($id);
            if($timeEntry->getId() !== NULL)
            {
                //Remove Entry
                if($timeEntry->remove($timeEntry->getId()))
                {
                    $this->view->message = "<i class=\"fa fa-check\"></i> Removed successfully.";
                }
                else
                {
                    $this->view->message = "ERROR: Cannot remove entry.";
                }
            }
            else
            {
                header("location: ".$this->_link(array('timesheet'))."");
            }
        }
        else
        {
            header("location: ".$this->_link(array('timesheet'))."");
        }
    }

    public function edit($id = null)
    {
        if($id != null)
        {
            $entry = new timeEntryModel($id);

            $data['inTime'] = $entry->getInTime();
            $data['outTime'] = $entry->getOutTime();
            $data['date'] = $entry->getDate();
            $data['lessTime'] = $entry->getLessTime();
            $data['code'] = $entry->getCodeId();

            $this->view->id = $entry->getId();

            $form = new editTimeForm();
            $form->setAction($this->_link(array('timesheet','edit',$id)));
            $form->addData($data);

            //Check for form submission
            if($form->wasSubmitted())
            {
                //Add submitted data to the form
                $form->addData($_POST);

                //Check form validation
                if($form->validate())
                {
                    //Export form data into an array
                    $data = $form->exportFormData();

                    //Check if dates are within the current pay period.
                    $startMonth = date('m',strtotime('last month'));

                    if($startMonth == 1)
                    {
                        $startYear = date('Y',strtotime('last year'));
                    }
                    else
                    {
                        $startYear = date('Y');
                    }

                    $endMonth = date('m');
                    $endYear = date('Y');

                    $startDate= strtotime($startMonth.'/26/'.$startYear);
                    $endDate = strtotime($endMonth.'/25/'.$endYear);

                    $userDate = strtotime($data['date']);

                    //Date is within pay period
                    if($userDate >= $startDate && $userDate <= $endDate)
                    {
                        //Create a new entry object and set properties
                        $entry = new timeEntryModel();
                        $entry->setId($id);
                        $entry->setDate($data['date']);
                        $entry->setInTime($data['inTime']);
                        $entry->setOutTime($data['outTime']);
                        $entry->setLessTime($data['lessTime']);
                        $entry->setCodeId($data['code']);

                        //Save entry data to table.
                        if($entry->save())
                        {
                            //Return a new time form with success message
                            $form->successMessage = array("<i class=\"fa fa-check\"></i> Entry saved for ".$data['date']."");
                            $this->view->form = $form;
                        }
                        else
                        {
                            //Return the same form with a warning message
                            $message = "<i class=\"fa fa-warning\"></i> Cannot insert overlapping time entries. If you are updating an already existing entry, remove that entry and submit a new one.";
                            $form->errorMessage = array($message);
                            $this->view->form = $form;
                        }
                    }
                    else
                    {
                        //Return the same form with error message.
                        $form->errorMessage = array("<i class='fa fa-warning'></i> You may only submit time for the current date period.");
                        $this->view->form = $form;
                    }
                }
                else
                {
                    //Return form with invalid data.
                    $this->view->form = $form;
                }
            }
            else
            {
                //Return form
                $this->view->form = $form;
            }
        }
        else
        {
            header("location: ".$this->_link(array('timesheet'))."");
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
                header("location: ".$this->_link(array('timesheet',$data['year']))."");
            }
            else
            {
                header("location: ".$this->_link(array('timesheet'))."");
            }
        }
        else
        {
            header("location: ".$this->_link(array('timesheet'))."");
        }
    }

    public function validate($year, $month)
    {
        $timesheet = new timesheetModel($year,$month);

        //Get Current Batch ID
        $auth = Staple_Auth::get();
        $user = new userModel($auth->getAuthId());
        $batchId = $user->getBatchId();

        //Check for unvalidated entries within the current pay period.
        $i = 0;
        foreach($timesheet->getEntries() as $entry)
        {
            if($entry->inTimeRaw >= $timesheet->getStartDateTimeString() && $entry->inTimeRaw <= $timesheet->getEndDateTimeString())
            {
                if($entry->batchId == $timesheet->getBatch())
                {
                    $i++;
                }
            }
        }

        if($i > 0)
        {
            $this->view->timesheet = $timesheet;

            $form = new validateTimeSheetForm();
            $form->setAction($this->_link(array('timesheet','validate',$timesheet->getCurrentYear(),$timesheet->getCurrentMonth())));

            if($form->wasSubmitted())
            {
                if($entry->inTimeRaw >= $timesheet->getStartDateTimeString() && $entry->inTimeRaw <= $timesheet->getEndDateTimeString())
                {
                    $timesheet->validate($batchId);
                    header("location:" . $this->_link(array('timesheet')) . "");
                }
            }
            else
            {
                $this->view->form = $form;
                $this->view->needsValidation = false;
            }
        }
        else
        {
            $this->view->needsValidation = false;
            $this->view->timesheet = array();
        }

    }

    /* TODO REMOVE
    public function unlocked()
    {
        $form = new unlockDatesForm();

        if($form->wasSubmitted())
        {
            $form->addData($_POST);
            if($form->validate())
            {
                $data = $form->exportFormData();
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
    */

    public function admininsert()
    {
        if($this->accountLevel >= 900)
        {
            $form = new insertTimeForm();
            $form->admin(1);

            if($form->wasSubmitted())
            {
                $form->addData($_POST);
                if($form->validate())
                {
                    $data = $form->exportFormData();

                    //Create a new entry object and set properties
                    $entry = new timeEntryModel();
                    $entry->setDate($data['date']);
                    $entry->setInTime($data['inTime']);
                    $entry->setOutTime($data['outTime']);
                    $entry->setLessTime($data['lessTime']);
                    $entry->setCodeId($data['code']);
                    $entry->setUserId($data['account']);
                    $entry->setNote($data['note']);

                    //Save entry data to table.
                    if($entry->adminSave())
                    {
                        //Return a new time form with success message
                        $form = new insertTimeForm();
                        $form->admin(1);
                        $form->successMessage = array("<i class=\"fa fa-check\"></i> Entry saved for ".$data['date']."");
                        $this->view->form = $form;
                    }
                    else
                    {
                        //Return the same form with a warning message
                        $message = "<i class=\"fa fa-warning\"></i> Administrative action not allowed on your own timesheet.";
                        $form->errorMessage = array($message);
                        $this->view->form = $form;
                    }
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
        else
        {
            header("location: ".$this->_link(array('index'))."");
        }
    }
}
?>