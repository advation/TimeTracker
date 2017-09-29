<?php

class requestForLeaveForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('requestForLeaveFormLayout');

        $this->setName('requestForLeave')
         ->setAction($this->link(array('requests')));

        $startDate = new Staple_Form_FoundationTextElement('startDate','Desired Start Date');
        $startDate->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,20));

        $endDate = new Staple_Form_FoundationTextElement('endDate','Desired End Date');
        $endDate->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,20));

        $timeCodes = new codeModel();
        $code = new Staple_Form_FoundationSelectElement('code','Request Code');
        $code->setRequired()
            ->addOption("x","Select an option")
            ->addOptionsArray($timeCodes->requestCodes())
            ->addValidator(new Staple_Form_Validate_InArray(array_keys($timeCodes->requestCodes())));
        $code->setValue($timeCodes->getIdFor('Normal')['id']);

        $note = new Staple_Form_FoundationTextareaElement('note','Note');
        $note->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,1000))
            ->addAttrib("style", "height:100px;");

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($startDate,$endDate,$code,$note,$submit);
    }
}

?>