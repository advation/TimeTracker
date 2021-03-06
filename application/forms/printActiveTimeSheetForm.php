<?php

class printActiveTimeSheetForm extends Staple_Form
{
    public function _start()
    {
        //$this->setLayout('');

        $this->setName('printActiveTimeSheet')
            ->setAction($this->link(array('reports','inactive')));

        $account = new Staple_Form_FoundationSelectElement('account','Select an account');
        $account->setRequired()
            ->addOption('','Select an account')
            ->addOptionsArray($this->accounts())
            ->addValidator(new Staple_Form_Validate_InArray($this->accounts(1)));

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($account,$submit);
    }

    function accounts($ids = null)
    {
        $user = new userModel();
        $id = $user->getId();
        $authLevel = $user->getAuthLevel();

        $accounts = new userModel();
        $users = $accounts->listActive();
        $data = array();
        if($ids == null)
        {
            if(count($users) > 0)
            {
                foreach($users as $user)
                {

                    if($user['type'] == 'part')
                    {
                        $type = 'Part Time';
                    }

                    if($user['type'] == 'full')
                    {
                        $type = 'Full Time';
                    }

                    if($user['supervisorId'] == $id)
                    {
                        $data[$user['id']] = $user['lastName'].", ".$user['firstName']." ($type)";
                    }
                    elseif($authLevel >= 900)
                    {
                        $data[$user['id']] = $user['lastName'].", ".$user['firstName']." ($type)";
                    }
                }
            }
        }
        else
        {
            if(count($users) > 0)
            {
                foreach ($users as $user)
                {
                    $data[] = $user['id'];
                }
            }
        }

        return $data;
    }
}

?>