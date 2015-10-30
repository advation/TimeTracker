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

                if(strtotime($data['inTime']) < strtotime($data['outTime']))
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

    public function remove($id)
    {

    }

    public function edit($id = null)
    {
        if($id != null)
        {
            $timesheet = new timesheetModel();
            if($timesheet->exists($id))
            {
                $form = new editTimeForm();
                $form->setAction($this->_link(array('timesheet','edit',$id)));
                $form->addData($timesheet->entry($id));
                $form->id = $id;

                if($form->wasSubmitted())
                {
                    $form->addData($_POST);
                    if($form->validate())
                    {
                        $data = $form->exportFormData();
                        //Set Varibales
                        $userId = Staple_Auth::get();
                        $user = new userModel($userId->getAuthId());
                        $timesheet->setUserId($user->getId());
                        $timesheet->setDate($data['date']);
                        $timesheet->setInTime($data['inTime']);
                        $timesheet->setOutTime($data['outTime']);
                        $timesheet->setLessTime($data['lessTime']);
                        $timesheet->setCodeId($data['code']);

                        if($timesheet->save($id))
                        {
                            echo "Updated.";
                        }
                        else
                        {
                            echo "Not updated.";
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
                echo "Here";
                //header("location: ".$this->_link(array('timesheet'))."");
            }
        }
        else
        {
            echo "There";
            //header("location: ".$this->_link(array('timesheet'))."");
        }
    }
}
?>