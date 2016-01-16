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

		//$messages = new messagesModel();
		//$this->view->messages = array_merge($messages->getPrivateMessages(),$messages->getMessages());

		$date = new DateTime();
		$date->setTime(0,0,0);

		if($date->format('d') >= 26)
		{
			$date->modify('+1 month');
		}

		$date->setDate($date->format('Y'),$date->format('m'),1);

		$timesheet = new timesheetModel($date->format('Y'),$date->format('m'));
		$this->view->timesheet = $timesheet;

		$this->view->year = $date->format('Y');
		$this->view->month = $date->format('F');

		$date = new DateTime();
		$week = $date->format('W');
		$year = $date->format('Y');

		$report = new weeklyReportModel();

		$this->view->week = $report->getWeekWorked($this->userId, $week, $year);
	}
}
?>