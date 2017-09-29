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
                $startDate = strtotime($data['startDate']);
                $endDate = strtotime($data['endDate']);

                if($startDate <= $endDate)
                {
                    $_SESSION['startDate'] = $startDate;
                    $_SESSION['endDate'] = $endDate;
                    $_SESSION['requestData'] = $data;

                    $this->_redirect('requests/days');
                }
                else
                {
                    $form->addError("Date Error","Start date cannot be greater then end date");
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

    public function days()
    {
        $form = new requestForLeaveDaysForm();
        $this->view->form = $form;

        if($form->wasSubmitted())
        {
            $form->addData($_POST);
            if($form->validate())
            {
                echo "second form valid<br>";
                $data = $form->exportFormData();
                $_SESSION['requestData']['daysHours'] = $data;
                echo "<pre>";
                print_r($_SESSION['requestData']);
                echo "</pre>";
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

}
?>