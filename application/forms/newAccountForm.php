<?php

class newAccountForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('newAccountFormLayout');

        $this->setName('newAccount')
            ->setAction($this->link(array('accounts','index')));

        $pin = new Staple_Form_FoundationPasswordElement('pin','User PIN');
        $pin->setRequired()
            ->addAttrib("readonly","true")
            ->addValidator(new Staple_Form_Validate_Length(1,4))
            ->addValidator(new Staple_Form_Validate_Numeric());

        $firstName = new Staple_Form_FoundationTextElement('firstName','First Name');
        $firstName->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,40));

        $lastName = new Staple_Form_FoundationTextElement('lastName','Last Name');
        $lastName->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,40));

        $supervisor = new Staple_Form_FoundationSelectElement('supervisor','Select a Supervisor');
        $supervisor->setRequired()
            ->addOption("0","Select an account")
            ->addOptionsArray($this->accounts())
            ->addValidator(new Staple_Form_Validate_InArray($this->accounts(1)));

        $type = new Staple_Form_FoundationSelectElement('type','Set Account Type');
        $type->setRequired()
            ->addOption("","Select an account")
            ->addOptionsArray(array("part"=>"Part Time","full"=>"Full Time"))
            ->addValidator(new Staple_Form_Validate_InArray(array("part","full")));

        $level = new Staple_Form_FoundationSelectElement('level','Set Account Level');
        $level->setRequired()
            ->addOption("","Select a level")
            ->addOptionsArray(array("100"=>"Standard User","500"=>"Supervisor","900"=>"Administrator"))
            ->addValidator(new Staple_Form_Validate_InArray(array("100","500","900")));

        $pin = new Staple_Form_FoundationTextElement('pinNum','4 Digit PIN');
        $pin->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(4,4))
            ->addValidator(new Staple_Form_Validate_Numeric())
            ->addAttrib("maxlength","4");

        $pin2 = new Staple_Form_FoundationTextElement('pinNum2','Confirm 4 Digit PIN');
        $pin2->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(4,4))
            ->addValidator(new Staple_Form_Validate_Numeric())
            ->addAttrib("maxlength","4");

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($firstName, $lastName, $supervisor, $type, $level, $pin, $pin2, $submit);
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