<?php

class insertTimeForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('insertFormLayout');

        $this->setName('insertTimeForm')
            ->setAction($this->link(array('index','timesheet')));

        $date = new Staple_Form_FoundationDateElement('date','Date');
        $date->setRequired()
            ->addValidator(new Staple_Form_Validate_Length('1','10'));

        $inTime = new Staple_Form_FoundationTimeElement('inTime','Time In');
        $inTime->setRequired()
            ->addValidator(new Staple_Form_Validate_Length('1','8'));

        $outTime = new Staple_Form_FoundationTimeElement('outTime','Time Out');
        $outTime->setRequired()
            ->addValidator(new Staple_Form_Validate_Length('1','8'));

        $lessTime = new Staple_Form_FoundationSelectElement('lessTime','Less Time');
        $lessTime->setRequired()
            ->addOptionsArray(array("0"=>"None","60"=>"1 Hour","30"=>"30 Minutes","15"=>"15 Minutes"));

        $code = new Staple_Form_FoundationSelectElement('code','Code');
        $code->setRequired()
            ->addOptionsArray(array("normal"=>"Normal","vacation"=>"Vacation","sick"=>"Sick","holiday"=>"Holiday",));

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button radius');

        $this->addField($date, $inTime, $outTime, $lessTime, $code, $submit);
    }
}

?>