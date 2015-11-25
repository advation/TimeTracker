<?php

class weeklyReportForm extends Staple_Form
{
    public function _start()
    {
        //$this->setLayout('insertFormLayout');

        $this->setName('weeklyReportForm')
            ->setAction($this->link(array('reports','weekly')));

        $account = new Staple_Form_FoundationSelectElement('account','Account');
        $account->setRequired()
            ->addOption('','Select an account')
            ->addOptionsArray($this->accounts())
            ->addValidator(new Staple_Form_Validate_InArray($this->accounts(1)));

        $year = new Staple_Form_FoundationTextElement('year','Year');
        $year->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(4,4))
            ->addValidator(new Staple_Form_Validate_Numeric());

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($account, $year, $submit);
    }

    public function accounts($ids = null)
    {
        $accounts = new userModel();
        $users = $accounts->listAll();
        $data = array();
        if($ids == null)
        {
            foreach($users as $user)
            {
                $data[$user['id']] = $user['lastName'].", ".$user['firstName'];
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