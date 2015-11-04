<?php
class timesheetController extends Staple_Controller
{
    public function _start()
    {

    }

    public function index($month = null, $year = null)
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

                //Compare in Times and out Times.
                if(strtotime($data['inTime']) < strtotime($data['outTime']))
                {
                    //Create a new entry object
                    $entry = new timeEntryModel();
                    $entry->setDate($data['date']);
                    $entry->setInTime($data['inTime']);
                    $entry->setOutTime($data['outTime']);
                    $entry->setLessTime($data['lessTime']);
                    $entry->setCodeId($data['code']);

                    if($entry->save())
                    {
                        $this->view->message = "Entry saved.";
                    }
                    else
                    {
                        $this->view->message = "ERROR: Unable to save entry.";
                    }
                }
                else
                {
                    //Send form with error message back.
                    $form->message = array("<b>'Time In'</b> entry cannot be before <b>'Time Out'</b> entry.");
                    $this->view->insertTimeForm = $form;
                }
            }
            else
            {
                $this->view->insertTimeForm = $form;
            }
        }
        else
        {
            $this->view->insertTimeForm = $form;
        }

        //Load timesheet for user.
        $timesheet = new timesheetModel($month,$year);
        echo $timesheet->getStartDate()."<br>";
        echo $timesheet->getEndDate();

        //View
        $this->view->year = $timesheet->getYear();
        $this->view->nextYear = $timesheet->getNextYear();
        $this->view->previousYear = $timesheet->getPreviousYear();

        $this->view->month = $timesheet->getMonth();
        $this->view->previousMonth = $timesheet->getPreviousMonth();
        $this->view->nextMonth = $timesheet->getNextMonth();
    }

    public function remove($id)
    {
        if($id != null)
        {
            //Confirm entry for user
            $timesheet = new timesheetModel();
            if($timesheet->exists($id))
            {
                //Delete Item
                if($timesheet->remove($id))
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
            $entry = new timeEntryModel();
            if($entry->load($id))
            {
                $form = new editTimeForm();
                $form->setAction($this->_link(array('timesheet','edit',$id)));

            }
            else
            {
                echo "Entry loaded";
                //header("location: ".$this->_link(array('timesheet'))."");
            }
        }
        else
        {
            echo "ERROR: Unable to load entry";
            //header("location: ".$this->_link(array('timesheet'))."");
        }
    }
}
?>