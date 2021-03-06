<?php

	class userModel extends Staple_Model
	{
		private $db;

		private $id;
		private $username;
		private $firstName;
		private $lastName;
		private $type;
		private $authLevel;
		private $supervisorId;
		private $supervisorName;
		private $batchId;
		private $pin;

        /**
         * @return mixed
         */
        public function getSupervisorName()
        {
            return $this->supervisorName;
        }

        /**
         * @param mixed $supervisorName
         */
        public function setSupervisorName($supervisorName)
        {
            $this->supervisorName = $supervisorName;
        }

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
		public function getUsername()
		{
			return $this->username;
		}

		/**
		 * @param mixed $username
		 */
		public function setUsername($username)
		{
			$this->username = $username;
		}

		/**
		 * @return mixed
		 */
		public function getFirstName()
		{
			return $this->firstName;
		}

		/**
		 * @param mixed $firstName
		 */
		public function setFirstName($firstName)
		{
			$this->firstName = $firstName;
		}

		/**
		 * @return mixed
		 */
		public function getLastName()
		{
			return $this->lastName;
		}

		/**
		 * @param mixed $lastName
		 */
		public function setLastName($lastName)
		{
			$this->lastName = $lastName;
		}

		/**
		 * @return mixed
		 */
		public function getType()
		{
			return $this->type;
		}

		/**
		 * @param mixed $type
		 */
		public function setType($type)
		{
			$this->type = $type;
		}

		/**
		 * @return mixed
		 */
		public function getAuthLevel()
		{
			return $this->authLevel;
		}

		/**
		 * @param mixed $authLevel
		 */
		public function setAuthLevel($authLevel)
		{
			$this->authLevel = $authLevel;
		}

		/**
		 * @return mixed
		 */
		public function getSupervisorId()
		{
			return $this->supervisorId;
		}

		/**
		 * @param mixed $supervisorId
		 */
		public function setSupervisorId($supervisorId)
		{
			$this->supervisorId = $supervisorId;
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
		public function getPin()
		{
			return $this->pin;
		}

		/**
		 * @param mixed $pin
		 */
		public function setPin($pin)
		{
			$this->pin = $pin;
		}

		function __construct()
		{
			$this->db = Staple_DB::get();
			$auth = Staple_Auth::get();
			$username = $auth->getAuthId();
			$sql = "SELECT id, username, firstName, lastName, authLevel, batchId, supervisorId, type FROM accounts WHERE username = '".$this->db->real_escape_string($username)."'";

			if($this->db->query($sql)->fetch_row() > 0)
			{
				$query = $this->db->query($sql);
				$result = $query->fetch_assoc();

				$this->setid($result['id']);
				$this->setUsername($result['username']);
				$this->setFirstName($result['firstName']);
				$this->setLastName($result['lastName']);
				$this->setAuthLevel($result['authLevel']);
				$this->setBatchId($result['batchId']);
				$this->setSupervisorId($result['supervisorId']);
				$this->setType($result['type']);
			}
			else
			{
				return false;
			}
		}

		function getById($id)
		{
			$sql = "SELECT * FROM accounts WHERE id = '".$this->db->real_escape_string($id)."'";
			$query = $this->db->query($sql);
			$result = $query->fetch_assoc();
			return $result;
		}

		function userSupervisor()
        {
            $id = $this->getId();
            $sql = "SELECT * FROM accounts WHERE id = '".$this->db->real_escape_string($id)."'";
            $query = $this->db->query($sql);
            $result = $query->fetch_assoc();
            $superId = $result['supervisorId'];

            $sql = "SELECT username FROM accounts WHERE id = '".$this->db->real_escape_string($superId)."'";

            $query = $this->db->query($sql);
            $result = $query->fetch_assoc();

            return $result['username'];
        }

		function userInfo($id)
		{
			$sql = "SELECT id, username, firstName, lastName, authLevel, batchId, supervisorId, type, status FROM accounts WHERE id = '".$this->db->real_escape_string($id)."'";
			$query = $this->db->query($sql);
			$result = $query->fetch_assoc();
			return $result;
		}

		function listAll()
		{
			$sql = "SELECT id, username, firstName, lastName, authLevel, batchId, supervisorId, type, status FROM accounts WHERE status = 1 ORDER BY lastName ASC, firstName ASC";
			if($this->db->query($sql)->num_rows > 0)
			{
				$query = $this->db->query($sql);
				while($result = $query->fetch_assoc())
				{
					$data[] = $result;
				}
				return $data;
			}
		}

		function listActive()
		{
			$sql = "SELECT id, username, firstName, lastName, authLevel, batchId, supervisorId, type, status FROM accounts WHERE status = 1 ORDER BY lastName ASC, firstName ASC";
			if($this->db->query($sql)->num_rows > 0)
			{
				$query = $this->db->query($sql);
				while($result = $query->fetch_assoc())
				{
					$data[] = $result;
				}
				return $data;
			}
		}

		function listInactive()
		{
			$sql = "SELECT id, username, firstName, lastName, authLevel, batchId, supervisorId, type, status FROM accounts  WHERE status = 0 ORDER BY type DESC, lastName ASC, firstName ASC";
			if($this->db->query($sql)->num_rows > 0)
			{
				$query = $this->db->query($sql);
				while($result = $query->fetch_assoc())
				{
					$data[] = $result;
				}
				return $data;


			}
		}

		function assignedUsers()
        {
            $sql = "SELECT id, username, firstName, lastName, authLevel, batchId, supervisorId, type, status FROM accounts WHERE status = 1 AND supervisorId = '".$this->db->real_escape_string($this->getId())."' ORDER BY type DESC, lastName ASC, firstName ASC";
            if($this->db->query($sql)->num_rows > 0)
            {
                $query = $this->db->query($sql);
                while($result = $query->fetch_assoc())
                {
                    $data[] = $result;
                }
                return $data;
            }
        }
	}
?>