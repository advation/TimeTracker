<?php

class editTimeForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('editFormLayout');

        $this->setName('editTimeForm');

        $date = new Staple_Form_FoundationTextElement('date','Date');
        $date->setRequired()
            ->addValidator(new Staple_Form_Validate_Length('1','10'))
            ->addValidator(new Staple_Form_Validate_Date())
            ->addAttrib('placeholder','mm/dd/yyyy');

        $inTime = new Staple_Form_FoundationTextElement('inTime','Time In');
        $inTime->setRequired()
            ->addFilter(new Staple_Form_Filter_Trim())
            ->addValidator(new Staple_Form_Validate_Length('1','8'))
            ->addValidator(new Staple_Form_Validate_Regex('/^(0|[0-9]|1[012]):[0-5][0-9] ?((a|p)m|(A|P)M)$/','Invalid time format. Expected format: h:mm am/pm.'))
            ->addAttrib('placeholder','h:mm am/pm');

        $outTime = new Staple_Form_FoundationTextElement('outTime','Time Out');
        $outTime->setRequired()
            ->addFilter(new Staple_Form_Filter_Trim())
            ->addValidator(new Staple_Form_Validate_Length('1','8'))
            ->addValidator(new Staple_Form_Validate_Regex('/^(0|[0-9]|1[012]):[0-5][0-9] ?((a|p)m|(A|P)M)$/','Invalid time format. Expected format: h:mm am/pm.'))
            ->addAttrib('placeholder','h:mm am/pm');;

        $lessTime = new Staple_Form_FoundationSelectElement('lessTime','Less Time');
        $lessTime->setRequired()
            ->addOptionsArray(array("0"=>"None","60"=>"1 Hour","30"=>"30 Minutes"))
            ->addValidator(new Staple_Form_Validate_InArray(array('0','60','30')));

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