<?php

class editAccountForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('editAccountFormLayout');
        $this->setName('editAccount');

        $firstName = new Staple_Form_FoundationTextElement('firstName','First Name');
        $firstName->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,40));

        $lastName = new Staple_Form_FoundationTextElement('lastName','Last Name');
        $lastName->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,40));

        $userName = new Staple_Form_FoundationTextElement('username','User Name');
        $userName->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,40));

        $supervisor = new Staple_Form_FoundationSelectElement('supervisor','Supervisor');
        $supervisor->setRequired()
            ->addOption("0","Select an account")
            ->addOptionsArray($this->accounts())
            ->addValidator(new Staple_Form_Validate_InArray($this->accounts(1)));

        $type = new Staple_Form_FoundationSelectElement('type','Account Type');
        $type->setRequired()
            ->addOption("","Select an account")
            ->addOptionsArray(array("part"=>"Part Time","full"=>"Full Time"))
            ->addValidator(new Staple_Form_Validate_InArray(array("part","full")));

        $level = new Staple_Form_FoundationSelectElement('level','Account Level');
        $level->setRequired()
            ->addOption("","Select a level")
            ->addOptionsArray(array("100"=>"Standard User","500"=>"Supervisor","900"=>"Administrator"))
            ->addValidator(new Staple_Form_Validate_InArray(array("100","500","900")));

        $status = new Staple_Form_FoundationSelectElement('status','Account Status');
        $status->setRequired()
            ->addOption("","Select a status")
            ->addOptionsArray(array("1"=>"Enabled","0"=>"Disabled"))
            ->addValidator(new Staple_Form_Validate_InArray(array("1","0")));

        $submit = new Staple_Form_FoundationSubmitElement('submit','Save');
        $submit->addClass('button radius expand');

        $this->addField($firstName, $lastName, $userName, $supervisor, $type, $level, $status, $submit);
    }

    public function accounts($ids = null)
    {
        $accounts = new userModel();
        $users = $accounts->listAll();
        $data = array();

        foreach($users as $user)
        {
            if($user['authLevel'] >= 500)
            {
                if($ids == 1)
                {
                    $data[] = $user['id'];
                }
                else
                {
                    $data[$user['id']] = $user['lastName'].", ".$user['firstName']."";
                }
            }
        }

        return $data;
    }
}

?>