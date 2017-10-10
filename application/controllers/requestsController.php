<?php
class requestsController extends Staple_Controller
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

    public function index()
    {
        $form = new requestForLeaveForm();

        if($form->wasSubmitted())
        {
            $form->addData($_POST);
            if($form->validate())
            {
                $data = $form->exportFormData();
                unset($data['submit']);
                unset($data['ident']);
                $startDate = strtotime($data['startDate']);
                $endDate = strtotime($data['endDate']);

                if($startDate == $endDate)
                {
                    $singleDay = true;
                }

                //Check for sundays
                if(date("l",$startDate) == 'Sunday')
                {
                    $sundayCheck = true;
                }

                if(date("l",$endDate) == 'Sunday')
                {
                    $sundayCheck = true;
                }

                if($startDate <= $endDate)
                {
                    if($sundayCheck == false)
                    {
                        $_SESSION['startDate'] = $startDate;
                        $_SESSION['endDate'] = $endDate;
                        $_SESSION['requestData'] = $data;

                        $this->_redirect('requests/days');
                    }
                    else
                    {
                        $form->addError("Date Error","Sundays are not permitted. Please select a date between Monday through Saturday for the desired start and end dates.");
                        $this->view->form = $form;
                        $this->view->formError = 1;
                    }
                }
                else
                {
                    $form->addError("Date Error","Start date cannot be greater then end date");
                    $this->view->form = $form;
                    $this->view->formError = 1;
                }
            }
            else
            {
                $this->view->form = $form;
                $this->view->formError = 1;
            }
        }
        else
        {
            $this->view->form = $form;
        }

        $requests = new requestModel();
        $this->view->requests = $requests->getAll();
    }

    public function request($requestId = null)
    {
        if($requestId != null)
        {
            $request = new requestModel();
            $request->load($requestId);
            $this->view->requestId = $request->getRequestId();
            $this->view->codeName = $request->getCodeName();
            $this->view->startDate = $request->getStartDate();
            $this->view->endDate = $request->getEndDate();
            $this->view->dateTimes = $request->getDateTimes();
            $this->view->requestDate = $request->getDateOfRequest();
            $this->view->totalHoursRequested = $request->getTotalHoursRequested();
            $this->view->note = $request->getNote();
            $this->view->status = $request->getStatus();
            $this->view->approvedBy = $request->getApprovedByName();
            $this->view->dateTime = $request->getDateTimes();
        }
        else
        {
            header("location: ".$this->_link(array('requests'))."");
        }
    }

    public function days()
    {
        $form = new requestForLeaveDaysForm();
        if(isset($_SESSION['startDate']) && isset($_SESSION['endDate']))
        {
            if($form->wasSubmitted())
            {
                $form->addData($_POST);
                if($form->validate())
                {
                    $data = $form->exportFormData();
                    unset($data['submit']);
                    unset($data['ident']);
                    $_SESSION['requestData']['daysHours'] = $data;
                    $data = $_SESSION['requestData'];

                    $request = new requestModel();
                    //Check if start or end dates already exist for a pending request for this user.


                    $this->view->request = $request->calculate($data);

                    unset($_SESSION['startDate']);
                    unset($_SESSION['endDate']);
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
            header("location: ".$this->_link(array('requests'))."");
        }
    }

    public function submit($requestId)
    {
        $request = new requestModel();
        $request->notifySupervisorEmail($requestId);
        $request->changeToPendingApproval($requestId);
        header("location: ".$this->_link(array('requests'))."");
    }

    public function remove($requestId)
    {
        $request = new requestModel();
        if($request->remove($requestId))
        {
            echo "Removed";
            //header("location: ".$this->_link(array('requests'))."");
        }
        else
        {
            echo "Not removed";
        }
    }

}
?>