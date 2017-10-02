<?php
class requestModel extends Staple_Model
{
    private $db;

    private $id;
    private $requestId;
    private $code;
    private $codeName;
    private $userId;
    private $startDate;
    private $endDate;
    private $dateTimes;
    private $totalHoursRequested;
    private $dateOfRequest;
    private $status;
    private $approvedById;

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
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param mixed $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getDateTimes()
    {
        return $this->dateTimes;
    }

    /**
     * @param mixed $dateTimes
     */
    public function setDateTimes($dateTimes)
    {
        $this->dateTimes = $dateTimes;
    }

    /**
     * @return mixed
     */
    public function getTotalHoursRequested()
    {
        return $this->totalHoursRequested;
    }

    /**
     * @param mixed $totalHoursRequested
     */
    public function setTotalHoursRequested($totalHoursRequested)
    {
        $this->totalHoursRequested = $totalHoursRequested;
    }

    /**
     * @return mixed
     */
    public function getDateOfRequest()
    {
        return $this->dateOfRequest;
    }

    /**
     * @param mixed $dateOfRequest
     */
    public function setDateOfRequest($dateOfRequest)
    {
        $this->dateOfRequest = $dateOfRequest;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getApprovedById()
    {
        return $this->approvedById;
    }

    /**
     * @param mixed $approvedById
     */
    public function setApprovedById($approvedById)
    {
        $this->approvedById = $approvedById;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();
    }

    function load($requestId)
    {
        $sql = "SELECT * FROM requests WHERE requestId = '".$requestId."'";
        return $this->db->query($sql)->fetch_assoc();
    }

    function calculate($data)
    {
        $user = new userModel();
        //Organize dates and times into a new array
        $numOfDays = count($data['daysHours'])/3;
        $dateTimes = $data['daysHours'];
        $newDateTimes = array();

        $code = new codeModel();
        $code->loadRequestCode($data['code']);
        $newDateTimes['code'] = $data['code'];
        $newDateTimes['codeName'] = $code->getName();
        $newDateTimes['requestId'] = sha1($user->getId()."".$data['code']."".$numOfDays."".time());
        $newDateTimes['note'] = $data['note'];
        $newDateTimes['startDate'] = $data['startDate'];
        $newDateTimes['endDate'] = $data['endDate'];
        $newDateTimes['totalHoursRequested'] = 0;
        for($i=0;$i<$numOfDays;$i++)
        {
            $day = array();
            $day['dateString'] = $dateTimes["day$i"];
            $day['date'] = strtotime($dateTimes["day$i"]);

            $times = array();
            $times['startTime'] = $dateTimes["inTimeDay$i"];
            $times['endTime'] = $dateTimes["outTimeDay$i"];

            //Calculate hours
            $totalHours = $this->calculateHours(strtotime($dateTimes["inTimeDay$i"]),strtotime($dateTimes["outTimeDay$i"]));
            $day['hoursRequested'] = $totalHours;

            //Combine arrays
            $day['times'] = $times;
            $newDateTimes['dateTimes'][] = $day;

            $newDateTimes['totalHoursRequested'] = $newDateTimes['totalHoursRequested'] + $totalHours;
        }
        $this->setRequestId($newDateTimes['requestId']);
        $this->setCode($newDateTimes['code']);
        $this->setCodeName($newDateTimes['codeName']);
        $this->setUserId($user->getId());
        $this->setStartDate($newDateTimes['startDate']);
        $this->setEndDate($newDateTimes['endDate']);
        $this->setDateTimes(json_encode($newDateTimes['dateTimes']));
        $this->setTotalHoursRequested($newDateTimes['totalHoursRequested']);
        $this->setDateOfRequest(date('Y-m-d'));
        $this->setStatus(0);
        $this->save();
        return $newDateTimes;
    }

    function calculateHours($startDate, $endDate)
    {
        $diff = $endDate - $startDate;
        $hours = $diff / ( 60 * 60 );
        $hours = round(($hours * 2),0)/ 2;
        $user = new userModel();
        $userType = $user->getType();
        if($userType == 'full' && $hours > 8)
        {
            $hours--;
        }

        return $hours;
    }

    function save()
    {
        if(isset($this->requestId) && isset($this->code) && isset($this->userId) && isset($this->startDate) && isset($this->endDate))
        {
            //Get current users ID.
            $user = new userModel();
            $userId = $user->getId();

            $sql = "
                INSERT INTO requests 
                (requestId, code, userId, startDate, endDate, dateTimes, totalHoursRequested, status) 
                VALUES
                ('".$this->db->real_escape_string($this->requestId)."',
                '".$this->db->real_escape_string($this->code)."',
                '".$this->db->real_escape_string($userId)."',
                '".$this->db->real_escape_string($this->startDate)."',
                '".$this->db->real_escape_string($this->endDate)."',
                '".$this->db->real_escape_string($this->dateTimes)."',
                '".$this->db->real_escape_string($this->totalHoursRequested)."',
                '".$this->db->real_escape_string($this->status)."'
                )";

            echo $sql;

            if($this->db->query($sql))
            {
                $audit = new auditModel();
                $audit->setUserId($user->getId());
                $audit->setAction('Request Generated');
                $audit->setItem($user->getUsername()." requested ".$this->codeName." from ".$this->startDate." to ".$this->endDate.".");
                $audit->save();

                return true;
            }
        }
    }

    function notifySupervisorEmail($requestId)
    {
        $request = $this->load($requestId);
        $superUser = new userModel();
        //$email = $superUser->userSupervisor()."@twinfallspubliclibrary.org";
        $email = "aday@twinfallspubliclibrary.org";
        $user = new userModel();
        $userInfo = $user->userInfo($request['userId']);
        $msg = $userInfo['firstName']." ".$userInfo['lastName']." has requested time off. Please login to http://timetracker to review.";
        mail($email, "New TimeTracker Request",$msg);
    }

    function remove($userId, $year, $month)
    {
        /*
        $sql = "DELETE FROM timesheetReview WHERE accountId = '".$userId."' AND  payPeriodMonth = '".$month."' AND payPeriodYear = '".$year."';";

        if($this->db->query($sql))
        {
            return true;
        }
        */
    }
}