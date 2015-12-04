<?php

class rangeUnlockForm extends Staple_Form
{
    public function _start()
    {
        //$this->setLayout('insertFormLayout');

        $this->setName('rangeUnlockForm')
            ->setAction($this->link(array('reports','unlock')));

        $startDate = new Staple_Form_FoundationTextElement('startDate','Start Date');
        $startDate->setRequired()
            ->addValidator(new Staple_Form_Validate_Date())
            ->addAttrib('placeholder','mm/dd/yyyy');

        $endDate = new Staple_Form_FoundationTextElement('endDate','End Date');
        $endDate->setRequired()
            ->addValidator(new Staple_Form_Validate_Date())
            ->addAttrib('placeholder','mm/dd/yyyy');

        $account = new Staple_Form_FoundationSelectElement('account','Account');
        $account->setRequired()
            ->addOption('','Select an account')
            ->addOptionsArray($this->accounts())
            ->addValidator(new Staple_Form_Validate_InArray($this->accounts(1)));

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($account, $startDate, $endDate, $submit);
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