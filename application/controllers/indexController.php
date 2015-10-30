<?php
class indexController extends Staple_Controller
{
	public function _start()
	{

	}

	public function index()
	{
		$messages = array("The library will be closed on Monday for whatever reason. Just remember to not come in!");
		//$this->view->messages = $messages;
	}

	public function timesheet()
	{
		$timesheet = new timesheetModel();
		$this->view->timesheet = $timesheet->load();

		$insertTimeForm = new insertTimeForm();

		if($insertTimeForm->wasSubmitted())
		{
			$insertTimeForm->addData($_POST);
			if($insertTimeForm->validate())
			{
				echo "Valid Form!";
			}
			else
			{
				$this->view->insertTimeForm = $insertTimeForm;
			}
		}
		else
		{
			$this->view->insertTimeForm = $insertTimeForm;
		}
	}

	public function insert()
	{

	}
}
?>