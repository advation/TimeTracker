<?php

    class validateTimeSheetForm extends Staple_Form
    {
        public function _start()
        {
            $this->setLayout('validateTimeSheetFormLayout');

            $this->setName('validateTimeSheet');

            $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
            $submit->addClass('button radius success expand');

            $this->addField($submit);
        }
    }

?>