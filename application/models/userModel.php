<?php

	class userModel extends Staple_Model
	{
		private $db;

		private $id;
		private $username;
		private $firstName;
		private $lastName;
		private $accountType;

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
		public function getAccountType()
		{
			return $this->accountType;
		}

		/**
		 * @param mixed $accountType
		 */
		public function setAccountType($accountType)
		{
			$this->accountType = $accountType;
		}

		function __construct()
		{
			$this->db = Staple_DB::get();

			$auth = Staple_Auth::get();
			$username = $auth->getAuthId();

			$sql = "SELECT id, username, firstName, lastName, accountType FROM accounts WHERE username = '".$this->db->real_escape_string($username)."'";
			if($this->db->query($sql)->fetch_row() > 0)
			{
				$query = $this->db->query($sql);
				$result = $query->fetch_assoc();

				$this->setid($result['id']);
				$this->setUsername($result['username']);
				$this->setFirstName($result['firstName']);
				$this->setLastName($result['lastName']);
				$this->setAccountType($result['accountType']);
			}
			else
			{
				return false;
			}
		}
	}
?>