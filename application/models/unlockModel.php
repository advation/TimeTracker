<?php

class unlockModel extends Staple_Model
{
    private $db;
    private $username;

    private $id;
    private $startTime;
    private $endTime;
    private $userId;

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
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
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

    function __construct()
    {
        $this->db = Staple_DB::get();
        $auth = Staple_Auth::get();
        $this->username = $auth->getAuthId();
    }

    function load($uid)
    {
        $sql = "SELECT * type FROM overrideDates WHERE username = '".$this->db->real_escape_string($uid)."'";

        if($this->db->query($sql)->fetch_row() > 0)
        {
            $query = $this->db->query($sql);
            $result = $query->fetch_assoc();

            $this->setId($result['id']);
            $this->setStartTime($result['startTime']);
            $this->setEndTime($result['startTime']);
        }
    }
}

?>