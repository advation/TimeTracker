<?php

class unlockModel extends Staple_Model
{
    private $db;
    private $username;

    private $id;
    private $startTime;
    private $endTime;
    private $userId;
    private $rangeDates;

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
        $date = new DateTime();
        $date->setTimestamp($this->startTime);
        $startTime = $date->format('m/d/Y');
        return $startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = strtotime($startTime);
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        $date = new DateTime();
        $date->setTimestamp($this->endTime);
        $endTime = $date->format('m/d/Y');
        return $endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = strtotime($endTime);
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
    public function getRangeDates()
    {
        return $this->rangeDates;
    }

    /**
     * @param mixed $rangeDates
     */

    public function setRangeDates($rangeDates)
    {
        $this->rangeDates = $rangeDates;
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

    function save()
    {
        if(isset($this->startTime) && !isset($this->id))
        {
            $sql = "
                INSERT INTO overrideDates (startTime, endTime, userId) VALUES ('".$this->db->real_escape_string($this->startTime)."','".$this->db->real_escape_string($this->endTime)."','".$this->db->real_escape_string($this->userId)."')
            ";

            if($this->db->query($sql))
            {
                $audit = new auditModel();
                $audit->setUserId($this->userId);
                $audit->setAction('Range unlock');
                $audit->setItem($this->username." unlocked dates from ".$this->getStartTime()." to ".$this->getEndTime());
                $audit->save();

                return True;
            }
        }
    }

    function unlock($id)
    {
       //get userid
        $sql = "
            SELECT userId FROM timeEntries WHERE id = '".$this->db->real_escape_string($id)."';
        ";

        if($this->db->query($sql)->num_rows > 0)
        {
            $query = $this->db->query($sql);
            $result = $query->fetch_assoc();
            $userId = $result['userId'];

            $user = new userModel();
            $user = $user->userInfo($userId);
            $userId = $user['id'];
            $batchId = $user['batchId'];

            //Check if it's for the same user.
            $currentUser = new userModel();
            if($currentUser->getId() != $userId)
            {
                $sql = "
                UPDATE timeEntries SET batchId = '".$this->db->real_escape_string($batchId)."' WHERE id = '".$this->db->real_escape_string($id)."'
                ";

                if($this->db->query($sql))
                {
                    $audit = new auditModel();
                    $audit->setUserId($userId);
                    $audit->setAction('Single unlock');
                    $audit->setItem($this->username." unlocked time entry ". $id);
                    $audit->save();

                    return true;
                }
            }

        }
    }

    function rangeDates($uid)
    {
        $sql = "
            SELECT * FROM overrideDates WHERE userId = '".$this->db->real_escape_string($uid)."'
        ";

        if($this->db->query($sql)->num_rows > 0)
        {
            $query = $this->db->query($sql);

            $rangeDays = array();
            $groups = array();
            $i=0;
            while($result = $query->fetch_assoc())
            {
                $date = new DateTime();
                $date->setTimestamp($result['startTime']);

                $date2 = new DateTime();
                $date2->setTimestamp($result['endTime']);

                $interval = $date->diff($date2);
                $days = $interval->days;
                $groups[$i]['days'] = $days;
                $groups[$i]['startTime'] = $result['startTime'];
                $groups[$i]['endTime'] = $result['endTime'];
                $i++;
            }

            $total=0;
            foreach($groups as $group)
            {
                $total += $group['days'];
            }

            foreach($groups as $group)
            {
                for($i=1;$i<=$total;$i++)
                {
                    $rangeDays[$i]['startTime'] = $group['startTime'] + (86400 * $i);
                    $rangeDays[$i]['endTime'] = $group['startTime'] + (86400 * $i) + 86400;
                    $rangeDays[$i]['formattedStart'] = date('Y-m-d D', $group['startTime'] + (86400 * $i));
                    $rangeDays[$i]['formattedEnd'] = date('Y-m-d D', $group['startTime'] + (86400 * $i) + 86400);
                }
            }
            return $rangeDays;
        }
    }
}

?>