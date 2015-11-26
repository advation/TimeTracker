<?php

class weeklyReportForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('weeklyReportFormLayout');

        $this->setName('weeklyReportForm')
            ->setAction($this->link(array('reports','weekly')));

        $account = new Staple_Form_FoundationSelectElement('account','Account');
        $account->setRequired()
            ->addOption('','Select an account')
            ->addOptionsArray($this->accounts())
            ->addValidator(new Staple_Form_Validate_InArray($this->accounts(1)));

        $year = new Staple_Form_FoundationTextElement('year','Year');
        $year->setRequired()
            ->setValue(date('Y'))
            ->addValidator(new Staple_Form_Validate_Length(4,4))
            ->addValidator(new Staple_Form_Validate_Numeric());

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($account, $year, $submit);
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
                    $data[$user['id']] = $user['lastName'].", ".$user['firstName'];
                }
                elseif($authLevel >= 900)
                {
                    $data[$user['id']] = $user['lastName'].", ".$user['firstName'];
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