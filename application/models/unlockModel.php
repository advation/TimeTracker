<?php

class unlockModel extends Staple_Model
{
    private $db;
    private $username;
    private $errors;

    private $id;
    private $date;
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
    public function getDate()
    {
        $d = new DateTime();
        $d->setTimestamp($this->date);
        return $d->format('Y-m-d');
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $date = strtotime($date);
        $d = new DateTime();
        $d->setTimestamp($date);
        $this->date = $d->format('U');
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
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }



    function __construct()
    {
        $this->db = Staple_DB::get();
        $auth = Staple_Auth::get();
        $this->username = $auth->getAuthId();
    }

    function load($uid)
    {
        $sql = "SELECT * FROM overrideDates WHERE userId = '".$this->db->real_escape_string($uid)."' ORDER BY date ASC";

        if($this->db->query($sql)->fetch_row() > 0)
        {
            $query = $this->db->query($sql);

            while($result = $query->fetch_assoc())
            {
                $data[] = $result;
            }
            return $data;
        }
    }

    function save()
    {
        if(isset($this->date) && !isset($this->id))
        {
            $user = new userModel();
            if($this->getUserId() != $user->getId())
            {
                //Check if date is in the currect pay period.
                $timesheet = new timesheetModel(date('Y'),date('m'));
                if($this->date < $timesheet->getStartDateTimeString())
                {
                    //Check for existing date
                    $sql = "SELECT id FROM overrideDates WHERE date = '".$this->db->real_escape_string($this->date)."' AND userId = '".$this->db->real_escape_string($this->userId)."'";
                    if($this->db->query($sql)->num_rows == 0)
                    {
                        //Check for already existing time entry
                        $sql = "SELECT FROM_UNIXTIME(inTime,'%Y-%m-%d') AS date FROM timeEntries WHERE userId = '".$this->db->real_escape_string($this->userId)."'";

                        $query = $this->db->query($sql);
                        $matchDates = 0;
                        while($result = $query->fetch_assoc())
                        {
                            $date = new DateTime();
                            $date->setTimestamp($this->date);
                            $submitDate = $date->format('Y-m-d');
                            if($result['date'] == $submitDate)
                            {
                                $matchDates++;
                            }
                        }

                        if($matchDates == 0)
                        {
                            $sql = "
                              INSERT INTO overrideDates (date, userId) VALUES ('".$this->db->real_escape_string($this->date)."','".$this->db->real_escape_string($this->userId)."')
                            ";

                            if($this->db->query($sql))
                            {
                                $audit = new auditModel();
                                $audit->setUserId($this->userId);
                                $audit->setAction('Date unlock');
                                $audit->setItem($this->username." unlocked date ".$this->getDate());
                                $audit->save();

                                return True;
                            }
                        }
                        else
                        {
                            $this->errors[] = 'Time entry already exists for this date.';
                        }
                    }
                    else
                    {
                        $this->errors[] = 'Unlock already submitted for this date.';
                    }
                }
                else
                {
                    $this->errors[]  = "Date cannot be part of the current pay period.";
                }
            }
            else
            {
                $this->errors[] = "Cannot unlock time entires for your own timesheet.";
            }
        }
    }

    function unlock($id)
    {
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
}

?>