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

        $expireDate = new Staple_Form_FoundationTextElement('expireDate','Expiration Date');
        $expireDate->setRequired()
            ->addValidator(new Staple_Form_Validate_Date())
            ->addAttrib('placeholder','mm/dd/yyyy');

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($expireDate, $message, $submit);
    }
}

?>