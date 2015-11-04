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

			//Totals
			$vacationTotal = $this->vacationEntries($this->startDate, $this->endDate);
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

		function vacationEntries($startDate,$endDate)
		{
			//Get user ID from Auth
			$user = new userModel();
			$userId = $user->getId();

			//Get vacation timecode ID.
			$code = new codeModel();
			$codes = $code->getIdFor('vacation');
			$timeCode = $codes['id'];

			$sql = "SELECT * FROM timeEntries WHERE inTime BETWEEN $this->startDateTimeString AND $this->endDateTimeString AND userId = $userId AND codeId = $timeCode";

			echo $sql;

			if($this->db->query($sql)->fetch_row() > 0)
			{
				$query = $this->db->query($sql);
				$result = $query->fetch_assoc();

				//Set inTime
				$inTime = new DateTime();
				$inTime->setTimestamp($result['inTime']);

				//$this->setInTime($inTime->format('h:i A'));
				$vacationInTime = $inTime->format('h:i A');

				//$this->setInTimeRaw($result['inTime']);
				$vacationInTimeRaw = $result['inTime'];

				//$this->setRoundedInTime($this->nearestQuarterHour($result['inTime']));

				/*
				//Out Time
				$outTime = new DateTime();
				$outTime->setTimestamp($result['outTime']);
				$this->setOutTime($outTime->format('h:i A'));
				$this->setOutTimeRaw($result['outTime']);
				$this->setRoundedOutTime($this->nearestQuarterHour($result['outTime']));

				$this->setLessTime($result['lessTime']);

				//Calculate Time Worked
				switch($result['lessTime'])
				{
					case 60:
						$lessTime = 1;
						break;
					case 30:
						$lessTime = 0.5;
						break;
					case 15:
						$lessTime = 0.25;
						break;
					default:
						$lessTime = 0;
				}

				//Total Worked Time
				$dateTime1 = new DateTime($this->roundedInTime);
				$dateTime2 = new DateTime($this->roundedOutTime);
				$interval = $dateTime1->diff($dateTime2);

				$timeWorked = $this->timeToDecimal($interval->h.":".$interval->i)-$lessTime;

				if($timeWorked !== 0)
				{
					$this->setTimeWorked($timeWorked);
				}
				else
				{
					$this->setTimeWorked(0);
				}

				//Get Code Information
				$code = new codeModel();
				$this->setCodeId($result['codeId']);
				$code->load($result['codeId']);
				$this->setCodeName($code->getName());

				return true;
				*/
			}
		}
	}

?>