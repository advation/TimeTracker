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

                if($data['pinNum'] == $data['pinNum2'])
                {
                    $user = new accountModel();

                    $user->setFirstName(ucfirst($data['firstName']));
                    $user->setLastName(ucfirst($data['lastName']));
                    $user->setSupervisorId($data['supervisor']);
                    $user->setType($data['type']);
                    $user->setAuthLevel($data['level']);
                    $user->setPin($data['pinNum']);

                    if($user->save())
                    {
                        $this->view->newUser = true;
                        $this->view->firstName = $user->getFirstName();
                        $this->view->lastName = $user->getLastName();
                        $this->view->tempPin = $user->getTempPin();
                        $form = new newAccountForm();
                        $this->view->form = $form;
                    }
                    else
                    {
                        $form->errorMessage = array("ERROR: Could not create account");
                        $this->view->form = $form;
                        $this->layout->addScriptBlock('$(document).ready(function() { $("#new").foundation("reveal", "open"); }); ');
                    }
                }
                else
                {
                    $form->errorMessage = array("PINs do not match");
                    $this->view->form = $form;
                    $this->layout->addScriptBlock('$(document).ready(function() { $("#new").foundation("reveal", "open"); }); ');
                }
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

    public function edit($id = null)
    {
        if($id != null)
        {
            $this->view->id = $id;
            $user = new accountModel();

            $form = new editAccountForm();
            $form->setAction($this->_link(array('accounts','edit',$id)));
            $form->addData($user->load($id));

            if($form->wasSubmitted())
            {
                $form->addData($_POST);
                if($form->validate())
                {
                    $data = $form->exportFormData();

                    $user = new accountModel();
                    $user->setId($id);
                    $user->setFirstName($data['firstName']);
                    $user->setLastName($data['lastName']);
                    $user->setUsername($data['username']);
                    $user->setSupervisorId($data['supervisor']);
                    $user->setType($data['type']);
                    $user->setAuthLevel($data['level']);
                    $user->setStatus($data['status']);

                    if($user->save())
                    {
                        $this->view->successMessage = array("Changes saved");
                        $form = new editAccountForm();
                        $form->addData($user->load($id));
                        $this->view->form = $form;
                    }
                    else
                    {
                        $this->view->errorMessage = array("User Name already being used. Please try a different User Name");
                        $form->view->form = $form;
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
            header("location: ".$this->_link(array('accounts'))."");
        }
    }

    public function resetpin($id = null)
    {
        if($id != null)
        {
            $user = new accountModel();
            if($user->resetpin($id))
            {
                $this->view->tempPin = $user->getTempPin();
            }
            else
            {
                echo "Unable to reset PIN.";
            }
        }
        else
        {
            header("location: ".$this->_link("accounts")."");
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