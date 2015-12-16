<?php

	class timeEntryModel extends Staple_Model
	{
		private $db;

		private $id;
        private $date;
        private $fullDate;
        private $inTime;
        private $inTimeRaw;
        private $roundedInTime;
        private $inTimeDate;
        private $outTime;
		private $outTimeRaw;
        private $roundedOutTime;
        private $outTimeDate;
		private $lessTime;
		private $codeId;
        private $codeName;
        private $timeWorked;
        private $batchId;
        private $userId;
        private $timestamp;
        private $note;

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
        public function getFullDate()
        {
            return $this->fullDate;
        }

        /**
         * @param mixed $fullDate
         */
        public function setFullDate($fullDate)
        {
            $this->fullDate = $fullDate;
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
        public function getInTimeDate()
        {
            return $this->inTimeDate;
        }

        /**
         * @param mixed $inTimeDate
         */
        public function setInTimeDate($inTimeDate)
        {
            $this->inTimeDate = $inTimeDate;
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
        public function getOutTimeDate()
        {
            return $this->outTimeDate;
        }

        /**
         * @param mixed $outTimeDate
         */
        public function setOutTimeDate($outTimeDate)
        {
            $this->outTimeDate = $outTimeDate;
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
        public function getTimestamp()
        {
            return $this->timestamp;
        }

        /**
         * @param mixed $timestamp
         */
        public function setTimestamp($timestamp)
        {
            $this->timestamp = $timestamp;
        }

        /**
         * @return mixed
         */
        public function getNote()
        {
            return $this->note;
        }

        /**
         * @param mixed $note
         */
        public function setNote($note)
        {
            $this->note = $note;
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
                    $this->setFullDate(date("l, F jS Y",$result['inTime']));

                    //Set inTime
                    $inTime = new DateTime();
                    $inTime->setTimestamp($result['inTime']);
                    $this->setInTime($inTime->format('g:i A'));
                    $this->setInTimeRaw($result['inTime']);
                    $this->setRoundedInTime($this->nearestQuarterHour($result['inTime']));
                    $this->setInTimeDate(date("Y-m-d", $result['inTime']));

                    //Out Time
                    $outTime = new DateTime();
                    $outTime->setTimestamp($result['outTime']);
                    $this->setOutTime($outTime->format('g:i A'));
                    $this->setOutTimeRaw($result['outTime']);
                    $this->setRoundedOutTime($this->nearestQuarterHour($result['outTime']));
                    $this->setOutTimeDate(date("Y-m-d", $result['outTime']));

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
                    $dateTime1->setDate(date('Y',strtotime($this->inTimeDate)), date('m',strtotime($this->inTimeDate)), date('d',strtotime($this->inTimeDate)));
                    $dateTime2 = new DateTime($this->roundedOutTime);
                    $dateTime2->setDate(date('Y',strtotime($this->outTimeDate)), date('m',strtotime($this->outTimeDate)), date('d',strtotime($this->outTimeDate)));
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

                    $this->setUserId($result['userId']);
                    $this->setTimestamp($result['timestamp']);
                    $this->setNote($result['note']);

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
                $accountLevel = $user->getAuthLevel();

                $entry = new timeEntryModel($id);
                $fullDate = $entry->getFullDate();
                $inTime = $entry->getInTime();
                $outTime = $entry->getOutTime();
                $effectedUserId = $entry->getUserId();

                $effectedUser = new userModel();
                $account = $effectedUser->userInfo($effectedUserId);

                //Check for admin account delete
                if($accountLevel >= 900)
                {
                    //Check for active admin account
                    if($account['id'] != $user->getId())
                    {
                        $sql = "DELETE FROM timeEntries WHERE id = '".$this->db->real_escape_string($id)."' AND userId <> '".$this->db->real_escape_string($userId)."'";

                        if($this->db->query($sql))
                        {
                            $audit = new auditModel();
                            $audit->setUserId($account['id']);
                            $audit->setAction('Admin Entry Remove');
                            $audit->setItem($user->getUsername()." removed entry for ".$fullDate." In Time: ".$inTime." Out Time: ".$outTime."");
                            $audit->save();

                            return true;
                        }
                    }
                }
                else
                {
                    //Check if validated
                    if($this->validated($id))
                    {
                        $sql = "DELETE FROM timeEntries WHERE id = '".$this->db->real_escape_string($id)."' AND userId = '".$this->db->real_escape_string($userId)."'";

                        if($this->db->query($sql))
                        {
                              return true;
                        }
                    }
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

            if(strtotime($this->getDate()." ".$this->getInTime()) > strtotime($this->getDate()." ".$this->getOutTime()))
            {
                $outTime = strtotime($this->getDate()." 12:00 AM")+86400;
            }

            if($this->id == NULL)
            {
                if($this->_overlap($inTime,$outTime))
                {
                    //Insert new item
                    $sql = "INSERT INTO timeEntries (userId, inTime, outTime, lessTime, codeId, batchId)
                    VALUES (
                        '" . $this->db->real_escape_string($userId) . "',
                        '" . $this->db->real_escape_string($inTime) . "',
                        '" . $this->db->real_escape_string($outTime) . "',
                        '" . $this->db->real_escape_string($this->getLessTime()) . "',
                        '" . $this->db->real_escape_string($this->getCodeId()) . "',
                        '" . $this->db->real_escape_string($batchId) . "'
                        )";

                    $query = $this->db->query($sql);
                    if ($query === true)
                    {
                        return true;
                    }
                }
            }
            else
            {
                if($this->_overlap($inTime,$outTime,$this->getId()))
                {
                    //Update item
                    $sql = "UPDATE timeEntries SET
                        inTime='" . $this->db->real_escape_string($inTime) . "',
                        outTime='" . $this->db->real_escape_string($outTime) . "',
                        lessTime='" . $this->db->real_escape_string($this->getLessTime()) . "',
                        codeId='" . $this->db->real_escape_string($this->getCodeId()) . "',
                        batchId='" . $this->db->real_escape_string($batchId) . "'
                        WHERE id='" . $this->db->real_escape_string($this->id) . "'
                    ";

                    $query = $this->db->query($sql);

                    if ($query === true)
                    {
                        return true;
                    }
                }
            }
		}

        function nearestQuarterHour($time,$string = null)
        {
            //$time = strtotime($time);
            $round = 15*60;
            $rounded = round($time/$round)*$round;

            if($string == 1)
            {
                return $rounded;
            }
            else
            {
                return date("g:i A", $rounded);
            }
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

        function _overlap($inTime,$outTime,$id = null)
        {
            //Checks to see if the times entered fit within any other time entry for that user.
            $this->db = Staple_DB::get();

            $auth = Staple_Auth::get();
            $user = new userModel($auth->getAuthId());
            $userId = $user->getId();

            /*
            $dateString = strtotime(date("Y-m-d", $inTime));
            $nextDateString = $dateString + 86400;
            */
            $date = new DateTime();
            $dateString = $inTime;
            $nextDateString = $date->setTimestamp($inTime)->setTime(23,59,59);
            $nextDateString = $nextDateString->format('U');

            //Find the earliest time for the given date.
            $sql = "
                SELECT inTime FROM timeEntries WHERE inTime > '".$this->db->real_escape_string($dateString)."' AND userId = '".$this->db->real_escape_string($userId)."' ORDER BY inTime ASC LIMIT 1
            ";

            $query = $this->db->query($sql);
            $result = $query->fetch_assoc();
            $firstInTime = $result['inTime'];

            //Find the latest time for the given date.
            $sql = "
                SELECT outTime FROM timeEntries WHERE outTime > '".$this->db->real_escape_string($dateString)."' AND outTime < '".$this->db->real_escape_string($nextDateString)."' AND userId = '".$this->db->real_escape_string($userId)."' ORDER BY outTime DESC LIMIT 1
            ";

            if($this->db->query($sql)->num_rows > 0)
            {
                $query = $this->db->query($sql);
                $result = $query->fetch_assoc();
                $lastOutTime = $result['outTime'];
            }
            else
            {
                $lastOutTime = null;
            }

            if($id == null)
            {
                $sql = "SELECT inTime, outTime FROM timeEntries WHERE userId = '".$this->db->real_escape_string($userId)."'";
            }
            else
            {
                $sql = "SELECT inTime, outTime FROM timeEntries WHERE userId = '".$this->db->real_escape_string($userId)."' AND id <> '".$this->db->real_escape_string($id)."'";
            }

            $query = $this->db->query($sql);
            $data = array();
            while($result = $query->fetch_assoc())
            {
                $data[] = $result;
            }

            $overlap = 0;
            foreach($data as $entry)
            {
                if($inTime == $entry['inTime'] && $outTime == $entry['outTime'])
                {
                    $overlap++;
                }

                if($inTime > $entry['inTime'] && $inTime < $entry['outTime'])
                {
                    $overlap++;
                }

                if($outTime > $entry['inTime'] && $outTime < $entry['outTime'])
                {
                    $overlap++;
                }

                if($inTime < $firstInTime && $outTime > $lastOutTime)
                {
                    //$overlap++;
                }
            }

            if($overlap > 0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }

        function validated($id,$uid = null)
        {
            if($uid == null)
            {
                $auth = Staple_Auth::get();
                $user = new userModel($auth->getAuthId());
                $userId = $user->getId();
                $batchId = $user->getBatchId();
            }
            else
            {
                $user = new userModel();
                $info = $user->userInfo($uid);
                $userId = $info['id'];
                $batchId = $info['batchId'];
            }

            $sql = "SELECT id FROM timeEntries WHERE userId = '".$this->db->real_escape_string($userId)."' AND batchId = '".$this->db->real_escape_string($batchId)."' AND id = '".$this->db->real_escape_string($id)."'";

            if($this->db->query($sql)->num_rows > 0)
            {
                return true;
            }
            else
            {
                return false;
            }

        }

        function adminSave()
        {
            if(isset($this->userId))
            {
                //Check for current account.
                $currentUser = new userModel();
                if($this->userId != $currentUser->getId())
                {
                    $inTime = strtotime($this->getDate()." ".$this->getInTime());
                    $outTime = strtotime($this->getDate()." ".$this->getOutTime());

                    $sql = "
                  INSERT INTO timeEntries
                  (userId,inTime,outTime,lessTime,codeId,note,batchId)
                  VALUES (
                  '".$this->db->real_escape_string($this->userId)."',
                  '".$this->db->real_escape_string($inTime)."',
                  '".$this->db->real_escape_string($outTime)."',
                  '".$this->db->real_escape_string($this->lessTime)."',
                  '".$this->db->real_escape_string($this->codeId)."',
                  '".$this->db->real_escape_string($this->note)."',
                  '".$this->db->real_escape_string("ADMIN ADD")."'
                  )
                ";

                    if($this->db->query($sql))
                    {
                        $user = new userModel();

                        $audit = new auditModel();
                        $audit->setUserId($this->userId);
                        $audit->setAction('Admin Entry Add');
                        $audit->setItem($user->getUsername()." added entry for ".$this->getDate().". In Time: ".$this->inTime."/Out Time: ".$this->outTime."");
                        $audit->save();

                        return true;
                    }
                }
            }
        }
	}
?>