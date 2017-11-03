<?php

class changeDateRangeForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout("changeDateRangeFormLayout");
        $this->setName('changeDateRangeForm')
            ->setAction($this->link(array('reports','requests')));

        $startDate = new Staple_Form_FoundationTextElement('startDate','Start Date');
        $startDate->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1))
            ->addClass('hasDatePicker');

        $endDate = new Staple_Form_FoundationTextElement('endDate','End Date');
        $endDate->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1))
            ->addClass('hasDatePicker');

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($startDate, $endDate, $submit);
    }
}

?>