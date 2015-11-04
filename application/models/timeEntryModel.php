<?php

	class timeEntryModel extends Staple_Model
	{
		private $db;

		private $id;
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

                    $this->setDate(date("m/d/Y",$result['inTime']));
                    $this->setInTime(date("h:i A",$result['inTime']));
                    $this->setOutTime(date("h:i A",$result['outTime']));
                    $this->setLessTime($result['lessTime']);
                    $this->setCodeId($result['codeId']);
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

            $inTime = strtotime($this->getDate()." ".$this->getInTime());
            $outTime = strtotime($this->getDate()." ".$this->getOutTime());

            if($this->getId() == NULL)
			{
				//Insert new item
				$sql = "INSERT INTO timeEntries (userId, inTime, outTime, lessTime, codeId)
					VALUES (
						'".$this->db->real_escape_string($userId)."',
						'".$this->db->real_escape_string($inTime)."',
						'".$this->db->real_escape_string($outTime)."',
						'".$this->db->real_escape_string($this->getLessTime())."',
						'".$this->db->real_escape_string($this->getCodeId())."'
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
                    codeId='".$this->db->real_escape_string($this->getCodeId())."'
					WHERE id='".$this->db->real_escape_string($this->getId())."'
				";
			}
			
			$query = $this->db->query($sql);
			
			if($query === true)
			{
				return true;
			}
		}
	}

?>