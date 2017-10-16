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

        $this->view->staffRequests = $requests->staffRequests();
    }

    public function all($option = null)
    {
        if($option == "completed")
        {
            $requests = new requestModel();
            $this->view->requests = $requests->allStaffRequests('completed');
            $this->view->completed = true;
        }
        else
        {
            $requests = new requestModel();
            $this->view->requests = $requests->allStaffRequests();
            $this->view->completed = false;
        }
    }

    public function request($requestId = null)
    {
        if($requestId != null)
        {
            $request = new requestModel();

            if($request->hasAccess($requestId))
            {
                $request->load($requestId);
                $this->view->requestId = $request->getRequestId();
                $user = new userModel();
                $userInfo = $user->userInfo($request->getUserId());
                $this->view->firstName = $userInfo['firstName'];
                $this->view->lastName = $userInfo['lastName'];
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
                $this->view->superNote = $request->getSuperNote();
                $this->view->error = 0;
            }
            else
            {
                $this->view->error = "ERROR: You do not have access to this request.";
            }
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

    public function approve($requestId)
    {
        $request = new requestModel();
        $request->approve($requestId);
        header("location: ".$this->_link(array('requests'))."");
    }

    public function decline($requestId)
    {
        $_SESSION['requestDeclineId'] = $requestId;
        $form = new declineCommentForm();
        if($form->wasSubmitted())
        {
            $form->addData($_POST);
            if($form->validate())
            {
                $data = $form->exportFormData();
                $declineNote = $data['note'];

                $request = new requestModel();
                $request->decline($requestId,$declineNote);
                header("location: ".$this->_link(array('requests'))."");
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

    public function remove($requestId)
    {
        $request = new requestModel();
        if($request->remove($requestId))
        {
            header("location: ".$this->_link(array('requests'))."");
        }
        else
        {
            header("location: ".$this->_link(array('requests'))."");
        }
    }

}
?>