<?php
class indexController extends Staple_Controller
{
	public function _start()
	{

	}

	public function index()
	{
		$messages = array("The library will be closed on Monday for whatever reason. Just remember to not come in!");
		$this->view->messages = $messages;
	}
}
?>