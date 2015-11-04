<?php

class changeYearForm extends Staple_Form
{
    public function _start()
    {
        //$this->setLayout('');

        $this->setName('changeYearForm');

        $year = new Staple_Form_FoundationSelectElement('year','Year');
        $year->setRequired();

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($year,$submit);
    }

    function getYears()
    {

    }
}

?>