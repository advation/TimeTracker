<?php

class declineCommentForm extends Staple_Form
{
    public function _start()
    {
        $this->setName('declineNote')
            ->setAction($this->link(array('requests','decline',$_SESSION['requestDeclineId'])));

        $note = new Staple_Form_FoundationTextareaElement('note','Decline note');
        $note->setRequired()
            ->addValidator(new Staple_Form_Validate_Length(1,2000))
            ->addAttrib("style","height:200px;");

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($note, $submit);
    }
}

?>