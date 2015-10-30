<?php

	class timesheetModel extends Staple_Model
	{
		private $db;

		private $id;
		private $userId;
        private $date;
		private $inTime;
		private $outTime;
		private $lessTime;
		private $codeId;

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param mixed $id
         */
        public function setId($id)
        {
            $this->id = $id;
        }

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
        public function getDate()
        {
            return $this->date;
        }

        /**
         * @param mixed $date
         */
        public function setDate($date)
        {
            $this->date = $date;
        }

        /**
         * @return mixed
         */
        public function getInTime()
        {
            return $this->inTime;
        }

        /**
         * @param mixed $inTime
         */
        public function setInTime($inTime)
        {
            $this->inTime = $inTime;
        }

        /**
         * @return mixed
         */
        public function getOutTime()
        {
            return $this->outTime;
        }

        /**
         * @param mixed $outTime
         */
        public function setOutTime($outTime)
        {
            $this->outTime = $outTime;
        }

        /**
         * @return mixed
         */
        public function getLessTime()
        {
            return $this->lessTime;
        }

        /**
         * @param mixed $lessTime
         */
        public function setLessTime($lessTime)
        {
            $this->lessTime = $lessTime;
        }

        /**
         * @return mixed
         */
        public function getCodeId()
        {
            return $this->codeId;
        }

        /**
         * @param mixed $codeId
         */
        public function setCodeId($codeId)
        {
            $this->codeId = $codeId;
        }

		function __construct()
		{
			$this->db = Staple_DB::get();
		}	
	
		function load()
		{
			$authId = Staple_Auth::get()->getAuthId();
			//Get User ID.

            if(isset($authId))
            {
                $sql = "SELECT id FROM accounts WHERE username = '".$this->db->real_escape_string($authId)."'";

                if($this->db->query($sql)->fetch_row() > 0)
                {
                    $query = $this->db->query($sql);
                    $result = $query->fetch_assoc();

                    $this->setUserId($result['id']);
                }

				if(isset($this->userId))
				{
					$sql = "SELECT * FROM timeEntries WHERE userId = '" . $this->db->real_escape_string($this->userId) . "' ORDER BY inTime ASC";

					if ($this->db->query($sql)->fetch_row() > 0)
					{
						$query = $this->db->query($sql);
						$data = array();
						while ($row = $query->fetch_assoc())
						{
							$data[] = $row;
						}

						foreach($data as $entry)
						{
							$code = new codeModel();
							$code->load($entry['codeId']);
							$codeName = $code->getName();

							$data2['id'] = $entry['id'];

							//Set Date Object
							$date = new DateTime();
							$date->setTimestamp($entry['inTime']);

							//Date
							$data2['date']['ymd'] = $date->format("Y-m-d");

							//Formatted Date
							$data2['date']['formatted'] = $date->format("F jS, Y");

							//Day
							$data2['date']['dayOfWeek'] = $date->format("l");

							//Day Abbreviated
							$data2['date']['dayShort'] = $date->format("D.");

							//MonthYear
							$data2['date']['my'] = $date->format("m/y");

                            //MonthDay
                            $data2['date']['md'] = $date->format("m/d");

                            //DateMonthYear
                            $data2['date']['mdy'] = $date->format("m/d/y");

							//In Time
							$data2['rawInTime'] = $date->format("g:i A");
							$data2['roundedInTime'] = $this->nearestQuarterHour($date->format("g:i A"));

							//Out Time
							$date->setTimestamp($entry['outTime']);
							$data2['rawOutTime'] = $date->format("g:i A");
							$data2['roundedOutTime'] = $this->nearestQuarterHour($date->format("g:i A"));

							//Less Time
							$data2['lessTime'] = $entry['lessTime'];

                            switch($entry['lessTime'])
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
							$dateTime1 = new DateTime($data2['roundedInTime']);
							$dateTime2 = new DateTime($data2['roundedOutTime']);
							$interval = $dateTime1->diff($dateTime2);

							$data2['timeWorked'] = $interval->h.":".$interval->i;

                            $timeWorked = $this->timeToDecimal($interval->h.":".$interval->i)-$lessTime;

                            if($timeWorked > 0)
                            {
                                $data2['timeWorkedDec'] = $timeWorked;
                            }
                            else
                            {
                                $data2['timeWorkedDec'] = 0;
                            }

							$data2['code'] = $codeName;

							$data3[] = $data2;
						}

						return $data3;
					}
					else
					{
						return array();
					}
				}
            }
		}

		private function nearestQuarterHour($time)
		{
			$time = strtotime($time);
			$round = 15*60;
			$rounded = round($time/$round)*$round;

			return date("g:i A", $rounded);
		}

		private function timeToDecimal($time)
		{
			$timeArr = explode(':', $time);
			$hours = $timeArr[0]*1;
			$minutes = $timeArr[1]/60;
			$dec = $hours + $minutes;

            if($dec > 0)
            {
                return round($dec,2);
            }
            else
            {
                return 0;
            }
		}

        function exists($id)
        {
            $sql = "SELECT id FROM timeEntries WHERE id = '".$this->db->real_escape_string($id)."'";

            if($this->db->query($sql)->fetch_row() > 0)
            {
                return true;
            }
        }

        function entry($id)
        {
            $sql = "SELECT * FROM timeEntries WHERE id = '".$this->db->real_escape_string($id)."'";

            if($this->db->query($sql)->fetch_row() > 0)
            {
                $query = $this->db->query($sql);
                $result = $query->fetch_assoc();

                $data['date'] = date("m/d/Y",$result['inTime']);
                $data['inTime'] = date("h:i A",$result['inTime']);
                $data['outTime'] = date("h:i A",$result['outTime']);
                $data['lessTime'] = $result['lessTime'];
                $data['code'] = $result['codeId'];

                return $data;

            }
            else
            {
                return array();
            }
        }

		function save($id = null)
		{
            $inTime = strtotime($this->getDate()." ".$this->getInTime());
            $outTime = strtotime($this->getDate()." ".$this->getOutTime());

            if($id == null)
			{
                echo "Inserting";
				//Insert new item
				$sql = "INSERT INTO timeEntries (userId, inTime, outTime, lessTime, codeId)
					VALUES (
						'".$this->db->real_escape_string($this->getUserId())."',
						'".$this->db->real_escape_string($inTime)."',
						'".$this->db->real_escape_string($outTime)."',
						'".$this->db->real_escape_string($this->getLessTime())."',
						'".$this->db->real_escape_string($this->getCodeId())."'
						)";
			}
			else
			{

                echo "Updating";
				//Update item
				$sql = "UPDATE timeEntries SET
					userId='".$this->db->real_escape_string($this->getUserId())."',
					inTime='".$this->db->real_escape_string($inTime)."',
					outTime='".$this->db->real_escape_string($outTime)."',
					lessTime='".$this->db->real_escape_string($this->getLessTime())."',
                    codeId='".$this->db->real_escape_string($this->getCodeId())."'
					WHERE id='".$this->db->real_escape_string($id)."'
				";
			}
			
			$query = $this->db->query($sql);
			
			if($query === true)
			{
				return true;
			}
			else
			{
				return false;
			}
			
		}

	}

?>