<?php

	class timesheetModel extends Staple_Model
	{
		private $db;

		private $currentYear;
		private $currentMonth;
		private $currentMonthText;
		private $startDate;
		private $startDateTimeString;
		private $endDate;
		private $endDateTimeString;

		private $nextMonth;
		private $nextMonthText;
		private $nextYear;

		private $previousMonth;
		private $previousMonthText;
		private $previousYear;

		private $entries;

		private $vacationUsed;
		private $normalWorked;
		private $sickUsed;

		/**
		 * @return string
		 */
		public function getCurrentYear()
		{
			return $this->currentYear;
		}

		/**
		 * @param string $currentYear
		 */
		public function setCurrentYear($currentYear)
		{
			$this->currentYear = $currentYear;
		}

		/**
		 * @return string
		 */
		public function getCurrentMonth()
		{
			return $this->currentMonth;
		}

		/**
		 * @param string $currentMonth
		 */
		public function setCurrentMonth($currentMonth)
		{
			$this->currentMonth = $currentMonth;
		}

		/**
		 * @return string
		 */
		public function getCurrentMonthText()
		{
			return $this->currentMonthText;
		}

		/**
		 * @param string $currentMonthText
		 */
		public function setCurrentMonthText($currentMonthText)
		{
			$this->currentMonthText = $currentMonthText;
		}

		/**
		 * @return string
		 */
		public function getStartDate()
		{
			return $this->startDate;
		}

		/**
		 * @param string $startDate
		 */
		public function setStartDate($startDate)
		{
			$this->startDate = $startDate;
		}

		/**
		 * @return DateTime
		 */
		public function getEndDate()
		{
			return $this->endDate;
		}

		/**
		 * @param DateTime $endDate
		 */
		public function setEndDate($endDate)
		{
			$this->endDate = $endDate;
		}

		/**
		 * @return string
		 */
		public function getNextMonth()
		{
			return $this->nextMonth;
		}

		/**
		 * @param string $nextMonth
		 */
		public function setNextMonth($nextMonth)
		{
			$this->nextMonth = $nextMonth;
		}

		/**
		 * @return string
		 */
		public function getNextMonthText()
		{
			return $this->nextMonthText;
		}

		/**
		 * @param string $nextMonthText
		 */
		public function setNextMonthText($nextMonthText)
		{
			$this->nextMonthText = $nextMonthText;
		}

		/**
		 * @return string
		 */
		public function getNextYear()
		{
			return $this->nextYear;
		}

		/**
		 * @param string $nextYear
		 */
		public function setNextYear($nextYear)
		{
			$this->nextYear = $nextYear;
		}

		/**
		 * @return string
		 */
		public function getPreviousMonth()
		{
			return $this->previousMonth;
		}

		/**
		 * @param string $previousMonth
		 */
		public function setPreviousMonth($previousMonth)
		{
			$this->previousMonth = $previousMonth;
		}

		/**
		 * @return string
		 */
		public function getPreviousMonthText()
		{
			return $this->previousMonthText;
		}

		/**
		 * @param string $previousMonthText
		 */
		public function setPreviousMonthText($previousMonthText)
		{
			$this->previousMonthText = $previousMonthText;
		}

		/**
		 * @return string
		 */
		public function getPreviousYear()
		{
			return $this->previousYear;
		}

		/**
		 * @param string $previousYear
		 */
		public function setPreviousYear($previousYear)
		{
			$this->previousYear = $previousYear;
		}

		/**
		 * @return mixed
		 */
		public function getEntries()
		{
			return $this->entries;
		}

		/**
		 * @param mixed $entries
		 */
		public function setEntries($entries)
		{
			$this->entries = $entries;
		}

		/**
		 * @return int
		 */
		public function getEndDateTimeString()
		{
			return $this->endDateTimeString;
		}

		/**
		 * @param int $endDateTimeString
		 */
		public function setEndDateTimeString($endDateTimeString)
		{
			$this->endDateTimeString = $endDateTimeString;
		}

		/**
		 * @return int
		 */
		public function getStartDateTimeString()
		{
			return $this->startDateTimeString;
		}

		/**
		 * @param int $startDateTimeString
		 */
		public function setStartDateTimeString($startDateTimeString)
		{
			$this->startDateTimeString = $startDateTimeString;
		}

		/**
		 * @return int
		 */
		public function getVacationUsed()
		{
			return $this->vacationUsed;
		}

		/**
		 * @param int $vacationUsed
		 */
		public function setVacationUsed($vacationUsed)
		{
			$this->vacationUsed = $vacationUsed;
		}

		/**
		 * @return int
		 */
		public function getNormalWorked()
		{
			return $this->normalWorked;
		}

		/**
		 * @param int $normalWorked
		 */
		public function setNormalWorked($normalWorked)
		{
			$this->normalWorked = $normalWorked;
		}

		/**
		 * @return int
		 */
		public function getSickUsed()
		{
			return $this->sickUsed;
		}

		/**
		 * @param int $sickUsed
		 */
		public function setSickUsed($sickUsed)
		{
			$this->sickUsed = $sickUsed;
		}

		function __construct($year, $month)
		{
			$this->db = Staple_DB::get();

			//Current Dates
			$currentDate = new DateTime();
			$currentDate->setDate($year, $month, 1);

			$this->currentYear = $currentDate->format('Y');
			$this->currentMonth = $currentDate->format('m');
			$this->currentMonthText = $currentDate->format('F');
			$this->startDate = $currentDate->modify('-1 month +25 day')->format('Y-m-d');
			$this->startDateTimeString = strtotime($this->startDate);
			$currentDate->setDate($year, $month, 1);
			$this->endDate = $currentDate->modify('+24 day')->format('Y-m-d');
			$this->endDateTimeString = strtotime($this->endDate);

			//Previous Dates
			$previousDate = new DateTime();
			$previousDate->setDate($year, $month, 1);
			$previousDate->modify('-1 month');
			$this->previousMonth = $previousDate->format('m');
			$this->previousMonthText = $previousDate->format('F');
			$previousDate->setDate($year, $month, 1);
			$previousDate->modify('-1 year');
			$this->previousYear = $previousDate->format('Y');

			//Future Dates
			$furtureDate = new DateTime();
			$furtureDate->setDate($year, $month, 1);
			$furtureDate->modify('+1 month');
			$this->nextMonth = $furtureDate->format('m');
			$this->nextMonthText = $furtureDate->format('F');
			$furtureDate->setDate($year, $month, 1);
			$furtureDate->modify('+1 year');
			$this->nextYear = $furtureDate->format('Y');

			//Time Entries
			$this->entries = $this->entries($this->startDate, $this->endDate);

			$timeCode = new codeModel();

			//Vacation Total
			$code = $timeCode->getIdFor('vacation');
			$this->vacationUsed = $this->calculatedTotals($code['id'],$this->startDate, $this->endDate);

			//Normal Total
			$code = $timeCode->getIdFor('normal');
			$this->normalWorked = $this->calculatedTotals($code['id'],$this->startDate, $this->endDate);

			//Sick Total
			$code = $timeCode->getIdFor('sick');
			$this->sickUsed = $this->calculatedTotals($code['id'],$this->startDate,$this->endDate);
		}

		function entries($startDate,$endDate)
		{
			//Get user ID from Auth
			$user = new userModel();
			$userId = $user->getId();

			$sql = "SELECT id FROM timeEntries WHERE inTime BETWEEN $this->startDateTimeString AND $this->endDateTimeString AND userId = $userId ORDER BY inTime ASC";
			if($this->db->query($sql)->num_rows > 0)
			{
				$query = $this->db->query($sql);

				while($result = $query->fetch_assoc())
				{
					$entry = new timeEntryModel($result['id']);
					$data[] = $entry;
				}
				return $data;
			}
			else
			{
				return array();
			}
		}

		function calculatedTotals($code,$startDate,$endDate)
		{
			//Get user ID from Auth
			$user = new userModel();
			$userId = $user->getId();

			$sql = "SELECT ROUND((TIME_TO_SEC(SEC_TO_TIME(SUM(outTime - inTime)-SUM(lessTime*60)))/3600)*4)/4 AS 'totalTime' FROM timeEntries WHERE inTime > UNIX_TIMESTAMP('$startDate 00:00:00') AND outTime < UNIX_TIMESTAMP('$endDate 00:00:00') AND userId = $userId AND codeId = $code;";

			if($this->db->query($sql)->num_rows > 0)
			{
				$query = $this->db->query($sql);
				$result = $query->fetch_assoc();
				return round($result['totalTime'],2);
			}
			else
			{
				return 0;
			}
		}
	}

?>