<?php

class adminAccountForm extends Staple_Form
{
	public function _start()
	{
		//$this->setLayout('accountFormLayout');

		$this->setName('adminAuth')
			->setAction($this->link(array('account','admin')));

		$user = new Staple_Form_FoundationTextElement('username','Account');
		$user->setRequired()
			->addValidator(new Staple_Form_Validate_Length(1,50));

		$password = new Staple_Form_FoundationPasswordElement('password','Password');
		$password->setRequired()
			->addValidator(new Staple_Form_Validate_Length(1,50));

		$submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
		$submit->addClass('button expand');

		$this->addField($user, $password, $submit);
	}	
}

?>