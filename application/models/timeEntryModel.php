<?php

	class timeEntryModel extends Staple_Model
	{
		private $db;

		private $id;
        private $date;
        private $inTime;
        private $inTimeRaw;
        private $roundedInTime;
        private $outTime;
		private $outTimeRaw;
        private $roundedOutTime;
		private $lessTime;
		private $codeId;
        private $codeName;
        private $timeWorked;
        private $batchId;

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
        public function getInTimeRaw()
        {
            return $this->inTimeRaw;
        }

        /**
         * @param mixed $inTimeRaw
         */
        public function setInTimeRaw($inTimeRaw)
        {
            $this->inTimeRaw = $inTimeRaw;
        }

        /**
         * @return mixed
         */
        public function getOutTimeRaw()
        {
            return $this->outTimeRaw;
        }

        /**
         * @param mixed $outTimeRaw
         */
        public function setOutTimeRaw($outTimeRaw)
        {
            $this->outTimeRaw = $outTimeRaw;
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

        /**
         * @return mixed
         */
        public function getCodeName()
        {
            return $this->codeName;
        }

        /**
         * @param mixed $codeName
         */
        public function setCodeName($codeName)
        {
            $this->codeName = $codeName;
        }

        /**
         * @return mixed
         */
        public function getTimeWorked()
        {
            return $this->timeWorked;
        }

        /**
         * @param mixed $timeWorked
         */
        public function setTimeWorked($timeWorked)
        {
            $this->timeWorked = $timeWorked;
        }

        /**
         * @return mixed
         */
        public function getRoundedInTime()
        {
            return $this->roundedInTime;
        }

        /**
         * @param mixed $roundedInTime
         */
        public function setRoundedInTime($roundedInTime)
        {
            $this->roundedInTime = $roundedInTime;
        }

        /**
         * @return mixed
         */
        public function getRoundedOutTime()
        {
            return $this->roundedOutTime;
        }

        /**
         * @param mixed $roundedOutTime
         */
        public function setRoundedOutTime($roundedOutTime)
        {
            $this->roundedOutTime = $roundedOutTime;
        }

        /**
         * @return mixed
         */
        public function getBatchId()
        {
            return $this->batchId;
        }

        /**
         * @param mixed $batchId
         */
        public function setBatchId($batchId)
        {
            $this->batchId = $batchId;
        }

		function __construct($id = null)
		{
            $this->db = Staple_DB::get();
			if($id !== null)
            {
                $sql = "SELECT * FROM timeEntries WHERE id = '".$this->db->real_escape_string($id)."'";

                if($this->db->query($sql)->fetch_row() > 0)
                {
                    $query = $this->db->query($sql);
                    $result = $query->fetch_assoc();

                    //Set ID and Date
                    $this->setId($result['id']);
                    $this->setBatchId($result['batchId']);
                    $this->setDate(date("m/d/Y",$result['inTime']));

                    //Set inTime
                    $inTime = new DateTime();
                    $inTime->setTimestamp($result['inTime']);
                    $this->setInTime($inTime->format('h:i A'));
                    $this->setInTimeRaw($result['inTime']);
                    $this->setRoundedInTime($this->nearestQuarterHour($result['inTime']));

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
                }
            }
		}

        function remove($id)
        {
            $this->db = Staple_DB::get();
            if($id !== null)
            {
                $auth = Staple_Auth::get();
                $user = new userModel($auth->getAuthId());
                $userId = $user->getId();

                $sql = "DELETE FROM timeEntries WHERE id = '".$this->db->real_escape_string($id)."' AND userId = '".$this->db->real_escape_string($userId)."'";

                if($this->db->query($sql))
                {
                    return true;
                }
            }
        }

		function save()
		{
            $this->db = Staple_DB::get();
            $auth = Staple_Auth::get();
            $user = new userModel($auth->getAuthId());
            $userId = $user->getId();
            $batchId = $user->getBatchId();

            $inTime = strtotime($this->getDate()." ".$this->getInTime());
            $outTime = strtotime($this->getDate()." ".$this->getOutTime());

            if($this->getId() == NULL)
			{
				//Insert new item
				$sql = "INSERT INTO timeEntries (userId, inTime, outTime, lessTime, codeId, batchId)
					VALUES (
						'".$this->db->real_escape_string($userId)."',
						'".$this->db->real_escape_string($inTime)."',
						'".$this->db->real_escape_string($outTime)."',
						'".$this->db->real_escape_string($this->getLessTime())."',
						'".$this->db->real_escape_string($this->getCodeId())."',
						'".$this->db->real_escape_string($batchId)."'
						)";
			}
			else
			{
				//Update item
				$sql = "UPDATE timeEntries SET
					userId='".$this->db->real_escape_string($userId)."',
					inTime='".$this->db->real_escape_string($inTime)."',
					outTime='".$this->db->real_escape_string($outTime)."',
					lessTime='".$this->db->real_escape_string($this->getLessTime())."',
                    codeId='".$this->db->real_escape_string($this->getCodeId())."',
                    batchId='".$this->db->real_escape_string($this->getBatchId())."',
					WHERE id='".$this->db->real_escape_string($batchId)."'
				";
			}
			
			$query = $this->db->query($sql);
			
			if($query === true)
			{
				return true;
			}
		}

        function nearestQuarterHour($time)
        {
            //$time = strtotime($time);
            $round = 15*60;
            $rounded = round($time/$round)*$round;

            return date("g:i A", $rounded);
        }

        function timeToDecimal($time)
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

	}
?>