<?php

class insertTimeForm extends Staple_Form
{
    private $accountLevel;
    private $adminAction;

    /**
     * @return mixed
     */
    public function getAdminAction()
    {
        return $this->adminAction;
    }

    /**
     * @param mixed $adminAction
     */
    public function setAdminAction($adminAction)
    {
        $this->adminAction = $adminAction;
    }

    /**
     * @return mixed
     */
    public function getAccountLevel()
    {
        return $this->accountLevel;
    }

    /**
     * @param mixed $accountLevel
     */
    public function setAccountLevel($accountLevel)
    {
        $this->accountLevel = $accountLevel;
    }

    public function _start()
    {
        $auth = Staple_Auth::get();
        $user = new userModel();
        $user->userInfo($auth->getAuthId());
        $this->accountLevel = $user->getAuthLevel();

        $this->setLayout('insertFormLayout');

        $this->setName('insertTimeForm')
            ->setAction($this->link(array('timesheet')));

        $date = new Staple_Form_FoundationTextElement('date','Date');
        $date->setRequired()
            ->addValidator(new Staple_Form_Validate_Date())
            ->addAttrib('placeholder','mm/dd/yyyy');

        $inTime = new Staple_Form_FoundationTextElement('inTime','Time In');
        $inTime->setRequired()
            ->addFilter(new Staple_Form_Filter_Trim())
            ->addValidator(new Staple_Form_Validate_Regex('/^(0|[0-9]|1[012]):[0-5][0-9] ?((a|p)m|(A|P)M)$/','Invalid time format. Expected format: h:mm am/pm.'))
            ->addAttrib('placeholder','h:mm am/pm');

        $outTime = new Staple_Form_FoundationTextElement('outTime','Time Out');
        $outTime->setRequired()
            ->addFilter(new Staple_Form_Filter_Trim())
            ->addValidator(new Staple_Form_Validate_Regex('/^(0|[0-9]|1[012]):[0-5][0-9] ?((a|p)m|(A|P)M)$/','Invalid time format. Expected format: h:mm am/pm.'))
            ->addAttrib('placeholder','h:mm am/pm');;

        $lessTime = new Staple_Form_FoundationSelectElement('lessTime','Less Time');
        $lessTime->setRequired()
            ->addOptionsArray(array("0"=>"None","60"=>"1 Hour","30"=>"30 Minutes"))
            ->addValidator(new Staple_Form_Validate_InArray(array('0','60','30')));

        $timeCodes = new codeModel();
        $code = new Staple_Form_FoundationSelectElement('code','Code');
        $code->setRequired()
            ->addOption("x","Select an option")
            ->addOptionsArray($timeCodes->allCodes())
            ->addValidator(new Staple_Form_Validate_InArray(array_keys($timeCodes->allCodes())));
        $code->setValue($timeCodes->getIdFor('Normal')['id']);

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($date, $inTime, $outTime, $lessTime, $code, $submit);
    }

    public function admin($key)
    {
        if($key == 1)
        {
            $this->setAdminAction(1);
            if($this->accountLevel >= 900)
            {
                if($this->adminAction == 1)
                {
                    $this->setAction($this->link(array('timesheet','admininsert')));
                    $this->setLayout('adminInsertFormLayout');

                    $account = new Staple_Form_FoundationSelectElement('account','Account');
                    $account->setRequired()
                        ->addOption('','Select an account')
                        ->addOptionsArray($this->accounts())
                        ->addValidator(new Staple_Form_Validate_InArray($this->accounts(1)));

                    $note = new Staple_Form_FoundationTextElement('note','Note');
                    $note->setRequired()
                        ->addValidator(new Staple_Form_Validate_Length(1,5000))
                        ->addFilter(new Staple_Form_Filter_Trim());

                    $this->addField($account, $note);
                }
            }
        }
        else
        {
            $this->setAdminAction(0);
        }

    }

    public function accounts($ids = null)
    {
        $user = new userModel();
        $id = $user->getId();
        $authLevel = $user->getAuthLevel();

        $accounts = new userModel();
        $users = $accounts->listAll();
        $data = array();
        if($ids == null)
        {
            foreach($users as $user)
            {
                if($user['supervisorId'] == $id)
                {
                    $data[$user['id']] = $user['lastName'].", ".$user['firstName']." (". $user['type'] .")";
                }
                elseif($authLevel >= 900)
                {
                    $data[$user['id']] = $user['lastName'].", ".$user['firstName']." (". $user['type'] .")";
                }
            }
        }
        else
        {
            foreach($users as $user)
            {
                $data[] = $user['id'];
            }
        }

        return $data;
    }
}

?>