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
		private $batchId;
		private $pin;

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

		function __construct($user = null)
		{
			$this->db = Staple_DB::get();

			if($user == null)
			{
				$auth = Staple_Auth::get();
				$username = $auth->getAuthId();
			}
			else
			{
				$username = $user;
			}

			$sql = "SELECT id, username, firstName, lastName, authLevel, batchId, supervisorId FROM accounts WHERE username = '".$this->db->real_escape_string($username)."'";
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
			}
			else
			{
				return false;
			}
		}
	}
?>