<?php
class indexController extends Staple_Controller
{
	private $authLevel;
	private $userId;

	public function _start()
	{
		$user = new userModel();
		$this->authLevel = $user->getAuthLevel();
		$this->userId = $user->getId();
	}

	public function index()
	{
		$this->view->authLevel = $this->authLevel;

		$messages = array("The library will be closed on Monday for whatever reason. Just remember to not come in!");
		//$this->view->messages = $messages;

		$timesheet = new timesheetModel(date('Y'),date('m'));
		$this->view->timesheet = $timesheet;

		$date = new DateTime();
		$week = $date->format('W');
		$year = $date->format('Y');

		$report = new weeklyReportModel();

		$this->view->week = $report->getWeekWorked($this->userId, $week, $year);
	}
}
?>