<?php

class auditUserSearchForm extends Staple_Form
{
    public function _start()
    {
        //$this->setLayout('accountFormLayout');

        $this->setName('auditUserSearch')
            ->setAction($this->link(array('audit')));

        $users = new Staple_Form_FoundationSelectElement('users','Limit to user');
        $users->setRequired()
            ->addOption('','Select an account')
            ->addOptionsArray($this->accounts())
            ->addValidator(new Staple_Form_Validate_InArray($this->accounts()));

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand');

        $this->addField($users, $submit);
    }

    public function accounts()
    {
        $accounts = new userModel();

        $data = array();

        foreach($accounts->listAll() as $user)
        {
            $data[$user['id']] = $user['lastName'].", ".$user['firstName'];
        }

        return $data;
    }

}

?>