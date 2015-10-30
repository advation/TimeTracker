<?php

class editTimeForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('editFormLayout');

        $this->setName('editTimeForm');

        $date = new Staple_Form_FoundationTextElement('date','Date');
        $date->setRequired()
            ->addValidator(new Staple_Form_Validate_Date())
            ->addAttrib("placeholder","Example: MM\DD\YYYY");

        $inTime = new Staple_Form_FoundationTextElement('inTime','Time In');
        $inTime->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,10));

        $outTime = new Staple_Form_FoundationTextElement('outTime','Time Out');
        $outTime->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,10));

        $lessTime = new Staple_Form_FoundationSelectElement('lessTime','Less Time');
        $lessTime->setRequired()
            ->addOptionsArray(array("0"=>"None","60"=>"1 Hour","30"=>"30 Minutes","15"=>"15 Minutes"));

        $timeCodes = new codeModel();
        $code = new Staple_Form_FoundationSelectElement('code','Code');
        $code->setRequired()
            ->addOptionsArray($timeCodes->allCodes());

        $submit = new Staple_Form_FoundationSubmitElement('submit','Update');
        $submit->addClass('button success expand radius');

        $this->addField($date, $inTime, $outTime, $lessTime, $code, $submit);
    }
}

?>