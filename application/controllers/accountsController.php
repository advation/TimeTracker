<?php

class accountsController extends Staple_Controller
{
    private $authLevel;

    public function _start()
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();
        if($this->authLevel < 900)
        {
            header("location:".$this->_link(array('index','index'))."");
        }
    }

    public function index()
    {
        $accounts = new userModel();
        $this->view->accounts = $accounts->listActive();
        $this->view->allAccounts = $accounts->listAll();

        $form = new newAccountForm();

        if($form->wasSubmitted())
        {
            $form->addData($_POST);
            if($form->validate())
            {
                $data = $form->exportFormData();

                print_r($data);

                $account = substr($data['firstName'],0,1).$data['lastName'];
                echo $account;

                $form = new newAccountForm();
                $this->view->form = $form;
            }
            else
            {
                $this->view->form = $form;
                $this->layout->addScriptBlock('$(document).ready(function() { $("#new").foundation("reveal", "open"); }); ');
            }
        }
        else
        {
            $this->view->form = $form;
        }

    }

    public function inactive()
    {
        $accounts = new userModel();
        $this->view->accounts = $accounts->listInactive();
        $this->view->allAccounts = $accounts->listAll();
    }
}

?>