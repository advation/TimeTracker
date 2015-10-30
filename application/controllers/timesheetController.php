<?php
class timesheetController extends Staple_Controller
{
    public function _start()
    {

    }

    public function index()
    {
        $timesheet = new timesheetModel();
        $this->view->timesheet = $timesheet->load();

        $insertTimeForm = new insertTimeForm();

        if($insertTimeForm->wasSubmitted())
        {
            $insertTimeForm->addData($_POST);
            if($insertTimeForm->validate())
            {
                $data = $insertTimeForm->exportFormData();

                if($data['inTime'] < $data['outTime'])
                {
                    //Set Varibales
                    $timesheet = new timesheetModel();
                    $userId = Staple_Auth::get();
                    $user = new userModel($userId->getAuthId());
                    $timesheet->setUserId($user->getId());
                    $timesheet->setDate($data['date']);
                    $timesheet->setInTime($data['inTime']);
                    $timesheet->setOutTime($data['outTime']);
                    $timesheet->setLessTime($data['lessTime']);
                    $timesheet->setCodeId($data['code']);

                    //Save
                    if($timesheet->save())
                    {
                        header("location:".$this->_link(array('timesheet'))."");
                    }
                    else
                    {
                        $this->view->message = "Unable to save entry.";
                    }
                }
                else
                {
                    $insertTimeForm->message = array("<b>'Time In'</b> entry cannot be before <b>'Time Out'</b> entry.");
                    $this->view->insertTimeForm = $insertTimeForm;
                }
            }
            else
            {
                $this->view->insertTimeForm = $insertTimeForm;
            }
        }
        else
        {
            $this->view->insertTimeForm = $insertTimeForm;
        }
    }

    public function timesheet()
    {

    }

    public function reports()
    {

    }
}
?>