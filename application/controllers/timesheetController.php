<?php
class timesheetController extends Staple_Controller
{
    public function _start()
    {

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
                    //Compare in Times and out Times.
                    /*
                    if(strtotime($data['inTime']) < strtotime($data['outTime']))
                    {
                    */
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
                    /*
                    }
                    else
                    {
                        //Return the same form with error message.
                        $form->errorMessage = array("<b>'Time In'</b> entry cannot be before <b>'Time Out'</b> entry.");
                        $this->view->insertTimeForm = $form;
                    }
                    */
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
            $year = $date->format('Y');
        }

        if($month == null)
        {
            $date = new DateTime();
            $month = $date->format('m');
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

    public function remove($id)
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
                    $this->view->message = "Entry removed.";
                }
                else
                {
                    $this->view->message = "ERROR: Could not remove entry.";
                }
            }
            else
            {
                //header("location: ".$this->_link(array('timesheet'))."");
            }
        }
        else
        {
            //header("location: ".$this->_link(array('timesheet'))."");
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
                        //Compare in Times and out Times.
                        //if(strtotime($data['inTime']) < strtotime($data['outTime']))
                        //{
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
                        //}
                        //else
                        //{
                            //Return the same form with error message.
                        //    $form->errorMessage = array("<i class='fa fa-warning'></i> <b>'Time In'</b> entry cannot be before <b>'Time Out'</b> entry.");
                        //    $this->view->form = $form;
                        //}
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
            $this->view->timesheet = $timesheet;

            $form = new validateTimeSheetForm();
            $form->setAction($this->_link(array('timesheet','validate',$timesheet->getCurrentYear(),$timesheet->getCurrentMonth())));

            if($form->wasSubmitted())
            {
                $timesheet->validate($batchId);
                header("location:".$this->_link(array('timesheet'))."");
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
}
?>