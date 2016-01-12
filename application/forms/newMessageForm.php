<?php

class newMessageForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('newMessageFormLayout');

        $this->setName('newMessageForm')
            ->setAction($this->link(array('messages','index')));

        $message = new Staple_Form_FoundationTextareaElement('message','Message');
        $message->setRequired()
            ->addAttrib("placeholder","1000 character limit")
            ->addValidator(new Staple_Form_Validate_Length(1,1000))
            ->addAttrib("style","height:200px;");

        $account = new Staple_Form_FoundationSelectElement('account','Send To');
        $account->setRequired()
            ->addOption('','Select an account')
            ->addOptionsArray($this->accounts())
            ->addValidator(new Staple_Form_Validate_InArray($this->accounts(1)));

        $expireDate = new Staple_Form_FoundationTextElement('expireDate','Expiration Date');
        $expireDate->setRequired()
            ->addValidator(new Staple_Form_Validate_Date())
            ->addAttrib('placeholder','mm/dd/yyyy');

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($account, $expireDate, $message, $submit);
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
            $data['all'] = "All Accounts";
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
            $data[] = "all";
            foreach($users as $user)
            {
                $data[] = $user['id'];
            }
        }

        return $data;
    }
}

?>