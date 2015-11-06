<?php

class accountForm extends Staple_Form
{
	public function _start()
	{
		$this->setLayout('accountFormLayout');

		$this->setName('auth')
			->setAction($this->link(array('account','index')));
		
		$pin = new Staple_Form_FoundationPasswordElement('pin','User PIN');
		$pin->setRequired()
			->addAttrib("readonly","true")
			->addValidator(new Staple_Form_Validate_Length(1,4))
			->addValidator(new Staple_Form_Validate_Numeric());

		$submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
		$submit->addClass('button expand hide');

		$this->addField($pin, $submit);
	}	
}

?>