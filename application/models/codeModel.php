<?php

	class codeModel extends Staple_Model
	{
		private $db;

		private $id;
		private $name;
		private $multiplier;
		private $description;

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
		public function getName()
		{
			return $this->name;
		}

		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}

		/**
		 * @return mixed
		 */
		public function getMultiplier()
		{
			return $this->multiplier;
		}

		/**
		 * @param mixed $multiplier
		 */
		public function setMultiplier($multiplier)
		{
			$this->multiplier = $multiplier;
		}

		/**
		 * @return mixed
		 */
		public function getDescription()
		{
			return $this->description;
		}

		/**
		 * @param mixed $description
		 */
		public function setDescription($description)
		{
			$this->description = $description;
		}

		function __construct()
		{
			$this->db = Staple_DB::get();
		}

		function load($id = NULL)
		{
			$sql = "SELECT * FROM timeCodes WHERE id = '" . $this->db->real_escape_string($id) . "'";
			if($this->db->query($sql)->fetch_row() > 0)
			{
				$query = $this->db->query($sql);
				$result = $query->fetch_assoc();

				$this->setId($result['id']);
				$this->setName($result['name']);
				$this->setMultiplier($result['multiplier']);
				$this->setDescription($result['description']);
				return true;
			}
		}

		function allCodes()
		{
			$sql = "SELECT id, name FROM timeCodes WHERE 1 ORDER BY name ASC";
			if($this->db->query($sql)->fetch_row() > 0)
			{
				$query = $this->db->query($sql);

				while($result = $query->fetch_assoc())
                {
                    $data[$result['id']] = $result['name'];
                }

				return $data;
			}
		}

		function getIdFor($term = null)
		{
			if($term !== null)
			{
				$sql = "SELECT id FROM timeCodes WHERE name like '%".$this->db->real_escape_string($term)."%'";
				if($this->db->query($sql)->fetch_row() > 0)
				{
					$query = $this->db->query($sql);
					$result = $query->fetch_assoc();

					return $result;
				}
			}
		}
	}
?>