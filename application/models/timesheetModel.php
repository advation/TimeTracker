<?php

	class timesheetModel extends Staple_Model
	{
		private $db;

		private $userId;
		private $startDate;
		private $month;
		private $nextMonth;
		private $previousMonth;
		private $nextYear;
		private $previousYear;
		private $year;
		private $endDate;
		private $entries;

		/**
		 * @return mixed
		 */
		public function getUserId()
		{
			return $this->userId;
		}

		/**
		 * @param mixed $userId
		 */
		public function setUserId($userId)
		{
			$this->userId = $userId;
		}

		/**
		 * @return mixed
		 */
		public function getStartDate()
		{
			return $this->startDate;
		}

		/**
		 * @param mixed $startDate
		 */
		public function setStartDate($startDate)
		{
			$this->startDate = $startDate;
		}

		/**
		 * @return mixed
		 */
		public function getMonth()
		{
			return $this->month;
		}

		/**
		 * @param mixed $month
		 */
		public function setMonth($month)
		{
			$this->month = $month;
		}

		/**
		 * @return mixed
		 */
		public function getNextMonth()
		{
			return $this->nextMonth;
		}

		/**
		 * @param mixed $nextMonth
		 */
		public function setNextMonth($nextMonth)
		{
			$this->nextMonth = $nextMonth;
		}

		/**
		 * @return mixed
		 */
		public function getPreviousMonth()
		{
			return $this->previousMonth;
		}

		/**
		 * @param mixed $previousMonth
		 */
		public function setPreviousMonth($previousMonth)
		{
			$this->previousMonth = $previousMonth;
		}

		/**
		 * @return mixed
		 */
		public function getNextYear()
		{
			return $this->nextYear;
		}

		/**
		 * @param mixed $nextYear
		 */
		public function setNextYear($nextYear)
		{
			$this->nextYear = $nextYear;
		}

		/**
		 * @return mixed
		 */
		public function getPreviousYear()
		{
			return $this->previousYear;
		}

		/**
		 * @param mixed $previousYear
		 */
		public function setPreviousYear($previousYear)
		{
			$this->previousYear = $previousYear;
		}

		/**
		 * @return mixed
		 */
		public function getYear()
		{
			return $this->year;
		}

		/**
		 * @param mixed $year
		 */
		public function setYear($year)
		{
			$this->year = $year;
		}

		/**
		 * @return mixed
		 */
		public function getEndDate()
		{
			return $this->endDate;
		}

		/**
		 * @param mixed $endDate
		 */
		public function setEndDate($endDate)
		{
			$this->endDate = $endDate;
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

		function __construct($month = null,$year = null)
		{
			$this->db = Staple_DB::get();

			//Get user ID from Auth
			$user = new userModel();
			$this->userId = $user->getId();

			if ($month == NULL) {
				$month = new DateTime();
				$month = $month->format('n');
			}

			if ($year == NULL) {
				$year = new DateTime();
				$year = $year->format('Y');
			}

			$dateObj = new DateTime();
			$dateObj->setDate($year, $month, 1);
			$end = $dateObj->modify('+24 day');
			$endTime = strtotime($end->format('Y-m-d'));
			$endDate = $end->format('Y-m-d');
			$this->setEndDate($endDate);
			$this->setMonth($end->format('F'));
			$this->setNextMonth($end->modify('+1 month')->format('m'));
			$this->setPreviousMonth($end->modify('-1 month')->format('m'));

			$this->setYear($end->format('Y'));
			$this->setNextYear($end->modify('+1 year')->format('Y'));
			$this->setPreviousYear($end->modify('-1 year')->format('Y'));

			$start = $dateObj->modify('-1 month +1 day');
			$startTime = strtotime($start->format('Y-m-d'));
			$startDate = $start->format('Y-m-d');
			$this->setStartDate($startDate);
		}
	}

?>