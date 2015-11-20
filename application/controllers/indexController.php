<?php
class indexController extends Staple_Controller
{
	private $authLevel;

	public function _start()
	{
		$auth = Staple_Auth::get();
		$user = new userModel();
		$this->authLevel = $user->getAuthLevel();
	}

	public function index()
	{
		$this->view->authLevel = $this->authLevel;

		$messages = array("The library will be closed on Monday for whatever reason. Just remember to not come in!");
		//$this->view->messages = $messages;

		$timesheet = new timesheetModel(date('Y'),date('m'));
		$this->view->timesheet = $timesheet;
	}
}
?>