<?php

class insertTimeForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('insertFormLayout');

        $this->setName('insertTimeForm')
            ->setAction($this->link(array('timesheet')));

        $date = new Staple_Form_FoundationTextElement('date','Date');
        $date->setRequired()
            ->addValidator(new Staple_Form_Validate_Date())
            ->addAttrib('placeholder','mm/dd/yyyy');

        $inTime = new Staple_Form_FoundationTextElement('inTime','Time In');
        $inTime->setRequired()
            ->addValidator(new Staple_Form_Validate_Regex('/^(0|[0-9]|1[012]):[0-5][0-9] ?((a|p)m|(A|P)M)$/','Invalid time format. Expected format: h:mm am/pm.'))
            ->addAttrib('placeholder','h:mm am/pm');

        $outTime = new Staple_Form_FoundationTextElement('outTime','Time Out');
        $outTime->setRequired()
            ->addValidator(new Staple_Form_Validate_Regex('/^(0|[0-9]|1[012]):[0-5][0-9] ?((a|p)m|(A|P)M)$/','Invalid time format. Expected format: h:mm am/pm.'))
            ->addAttrib('placeholder','h:mm am/pm');;

        $lessTime = new Staple_Form_FoundationSelectElement('lessTime','Less Time');
        $lessTime->setRequired()
            ->addOptionsArray(array("0"=>"None","60"=>"1 Hour","30"=>"30 Minutes","15"=>"15 Minutes"))
            ->addValidator(new Staple_Form_Validate_InArray(array('0','60','30','15')));

        $timeCodes = new codeModel();
        $code = new Staple_Form_FoundationSelectElement('code','Code');
        $code->setRequired()
            ->addOption("x","Select an option")
            ->addOptionsArray($timeCodes->allCodes())
            ->addValidator(new Staple_Form_Validate_InArray(array_keys($timeCodes->allCodes())));
        $code->setValue($timeCodes->getIdFor('Normal')['id']);

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($date, $inTime, $outTime, $lessTime, $code, $submit);
    }
}

?>