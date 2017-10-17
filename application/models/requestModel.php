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
    private $approvedByName;
    private $note;
    private $superNote;

    /**
     * @return mixed
     */
    public function getSuperNote()
    {
        return $this->superNote;
    }

    /**
     * @param mixed $superNote
     */
    public function setSuperNote($superNote)
    {
        $this->superNote = $superNote;
    }

    /**
     * @return mixed
     */
    public function getApprovedByName()
    {
        return $this->approvedByName;
    }

    /**
     * @param mixed $approvedByName
     */
    public function setApprovedByName($approvedByName)
    {
        $this->approvedByName = $approvedByName;
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
        $result = $this->db->query($sql)->fetch_assoc();
        $this->setId($result['id']);
        $this->setRequestId($result['requestId']);
        $this->setDateTimes(json_decode($result['dateTimes']));
        $this->setTotalHoursRequested($result['totalHoursRequested']);
        $this->setStartDate($result['startDate']);
        $this->setEndDate($result['endDate']);
        $this->setApprovedById($result['approvedById']);
        $user = new userModel();
        $user->setId($result['approvedById']);
        $supervisor = $user->userInfo($result['approvedById']);
        $superFirst = $supervisor['firstName'];
        $superLast = $supervisor['lastName'];
        $this->setApprovedByName($superFirst." ".$superLast);
        $this->setUserId($result['userId']);
        $code = new codeModel();
        $code->load($result['code']);
        $this->setCodeName($code->getName());
        $this->setCode($result['code']);
        $this->setDateOfRequest($result['dateOfRequest']);
        $this->setNote($result['note']);
        $this->setStatus($result['status']);
        $this->setSuperNote($result['superNote']);
    }

    function allStaffRequests($type = null)
    {
        //Check for administrator level
        $user = new userModel();
        if($user->getAuthLevel() == 900)
        {
            $date = new DateTime();
            $date->modify("-4 weeks");
            $expireDate = $date->format('Y-m-d');

            if($type == "completed")
            {
                $sql = "
                  SELECT * FROM requests 
                  WHERE status = '1' OR status = '2'
                  AND dateOfRequest >= '".$this->db->real_escape_string($expireDate)."'
                  ORDER BY userId ASC, status ASC, dateOfRequest DESC";
            }
            else
            {
                $sql = "
                  SELECT * FROM requests 
                  WHERE status = '0'
                  AND dateOfRequest >= '".$this->db->real_escape_string($expireDate)."'
                  ORDER BY status ASC, userId ASC, dateOfRequest DESC";
            }

            $result = $this->db->query($sql);
            $code = new codeModel();
            $data = array();
            $i = 0;
            while($row = $result->fetch_assoc())
            {
                $code->loadRequestCode($row['code']);
                $data[$i]['id'] = $row['id'];
                $data[$i]['userId'] = $row['userId'];
                $user = new userModel();
                $staff = $user->userInfo($row['userId']);
                $data[$i]['firstName'] = $staff['firstName'];
                $data[$i]['lastName'] = $staff['lastName'];
                $data[$i]['requestId'] = $row['requestId'];
                $data[$i]['code'] = $row['code'];
                $data[$i]['codeName'] = $code->getName();
                $data[$i]['startDate'] = $row['startDate'];
                $data[$i]['endDate'] = $row['endDate'];
                $data[$i]['totalHoursRequested'] = $row['totalHoursRequested'];
                $data[$i]['dateOfRequest'] = $row['dateOfRequest'];
                $data[$i]['note'] = $row['note'];
                $data[$i]['dateTimes'] = json_decode($row['dateTimes']);
                $data[$i]['status'] = $row['status'];
                $data[$i]['superNote'] = $row['superNote'];
                $i++;
            }

            return $data;
        }
        else
        {
            return array();
        }
    }

    function requestStaffArchive()
    {
        $user = new userModel();
        $date = new DateTime();
        $date->modify("-4 weeks");
        $expireDate = $date->format('Y-m-d');

        if($user->getAuthLevel() == 900)
        {
            $sql = "
                    SELECT * FROM requests WHERE 
                    dateOfRequest <= '".$this->db->real_escape_string($expireDate)."'
                    ORDER BY status ASC, userId ASC, dateOfRequest DESC";

            $result = $this->db->query($sql);

            $code = new codeModel();
            $i = 0;
            $data = array();
            while($row = $result->fetch_assoc())
            {
                $code->loadRequestCode($row['code']);
                $data[$i]['id'] = $row['id'];
                $data[$i]['userId'] = $row['userId'];
                $user = new userModel();
                $staff = $user->userInfo($row['userId']);
                $data[$i]['firstName'] = $staff['firstName'];
                $data[$i]['lastName'] = $staff['lastName'];
                $data[$i]['requestId'] = $row['requestId'];
                $data[$i]['code'] = $row['code'];
                $data[$i]['codeName'] = $code->getName();
                $data[$i]['startDate'] = $row['startDate'];
                $data[$i]['endDate'] = $row['endDate'];
                $data[$i]['totalHoursRequested'] = $row['totalHoursRequested'];
                $data[$i]['dateOfRequest'] = $row['dateOfRequest'];
                $data[$i]['note'] = $row['note'];
                $data[$i]['dateTimes'] = json_decode($row['dateTimes']);
                $data[$i]['status'] = $row['status'];
                $data[$i]['superNote'] = $row['superNote'];
                $i++;
            }

            return $data;
        }
        else {
            //Get a list of staff under this user.
            $user = new userModel();
            $sql = "SELECT id, firstName, lastName, username, type FROM accounts WHERE supervisorId = '" . $this->db->real_escape_string($user->getId()) . "'";
            $result = $this->db->query($sql);
            $staff = array();
            while ($row = $result->fetch_assoc()) {
                $staff[] = $row;
            }
            $data = array();

            if (count($staff) > 0) {
                $i = 0;
                $date = new DateTime();
                $date->modify("-4 weeks");
                $expireDate = $date->format('Y-m-d');

                foreach ($staff as $user) {
                    $sql = "
                    SELECT * FROM requests WHERE 
                    userId = '" . $this->db->real_escape_string($user['id']) . "' AND
                    dateOfRequest <= '" . $this->db->real_escape_string($expireDate) . "'
                    ORDER BY status ASC, userId ASC, dateOfRequest DESC";

                    $result = $this->db->query($sql);

                    $code = new codeModel();

                    while ($row = $result->fetch_assoc()) {
                        $code->loadRequestCode($row['code']);
                        $data[$i]['id'] = $row['id'];
                        $data[$i]['userId'] = $row['userId'];
                        $user = new userModel();
                        $staff = $user->userInfo($row['userId']);
                        $data[$i]['firstName'] = $staff['firstName'];
                        $data[$i]['lastName'] = $staff['lastName'];
                        $data[$i]['requestId'] = $row['requestId'];
                        $data[$i]['code'] = $row['code'];
                        $data[$i]['codeName'] = $code->getName();
                        $data[$i]['startDate'] = $row['startDate'];
                        $data[$i]['endDate'] = $row['endDate'];
                        $data[$i]['totalHoursRequested'] = $row['totalHoursRequested'];
                        $data[$i]['dateOfRequest'] = $row['dateOfRequest'];
                        $data[$i]['note'] = $row['note'];
                        $data[$i]['dateTimes'] = json_decode($row['dateTimes']);
                        $data[$i]['status'] = $row['status'];
                        $data[$i]['superNote'] = $row['superNote'];
                        $i++;
                    }
                }
            }
        }

        return $data;
    }

    function requestArchive()
    {
        //Check for administrator level
        $user = new userModel();

        $date = new DateTime();
        $date->modify("-4 weeks");
        $expireDate = $date->format('Y-m-d');

        $user = new userModel();

        //Pulls archive for a specific staff member
        $sql = "
          SELECT * FROM requests 
          WHERE userId = '".$user->getId()."' 
          AND dateOfRequest <= '".$this->db->real_escape_string($expireDate)."'
          ORDER BY status ASC, userId ASC, dateOfRequest DESC";
        $result = $this->db->query($sql);
        $code = new codeModel();
        $data = array();
        $i = 0;
        while($row = $result->fetch_assoc())
        {
            $code->loadRequestCode($row['code']);
            $data[$i]['id'] = $row['id'];
            $data[$i]['userId'] = $row['userId'];
            $user = new userModel();
            $staff = $user->userInfo($row['userId']);
            $data[$i]['firstName'] = $staff['firstName'];
            $data[$i]['lastName'] = $staff['lastName'];
            $data[$i]['requestId'] = $row['requestId'];
            $data[$i]['code'] = $row['code'];
            $data[$i]['codeName'] = $code->getName();
            $data[$i]['startDate'] = $row['startDate'];
            $data[$i]['endDate'] = $row['endDate'];
            $data[$i]['totalHoursRequested'] = $row['totalHoursRequested'];
            $data[$i]['dateOfRequest'] = $row['dateOfRequest'];
            $data[$i]['note'] = $row['note'];
            $data[$i]['dateTimes'] = json_decode($row['dateTimes']);
            $data[$i]['status'] = $row['status'];
            $data[$i]['superNote'] = $row['superNote'];
            $i++;
        }

        return $data;
    }

    function staffRequests()
    {
        //Get a list of staff under this user.
        $user = new userModel();
        $sql = "SELECT id, firstName, lastName, username, type FROM accounts WHERE supervisorId = '".$this->db->real_escape_string($user->getId())."'";
        $result = $this->db->query($sql);
        $staff = array();
        while($row = $result->fetch_assoc())
        {
            $staff[] = $row;
        }
        $data = array();

        if(count($staff) > 0)
        {
            $i = 0;
            $date = new DateTime();
            $date->modify("-4 weeks");
            $expireDate = $date->format('Y-m-d');

            foreach($staff as $user)
            {
                $sql = "
                    SELECT * FROM requests WHERE 
                    userId = '".$this->db->real_escape_string($user['id'])."' AND
                    dateOfRequest >= '".$this->db->real_escape_string($expireDate)."'
                    ORDER BY status ASC, userId ASC, dateOfRequest DESC";

                $result = $this->db->query($sql);

                $code = new codeModel();

                while($row = $result->fetch_assoc())
                {
                    $code->loadRequestCode($row['code']);
                    $data[$i]['id'] = $row['id'];
                    $data[$i]['userId'] = $row['userId'];
                    $user = new userModel();
                    $staff = $user->userInfo($row['userId']);
                    $data[$i]['firstName'] = $staff['firstName'];
                    $data[$i]['lastName'] = $staff['lastName'];
                    $data[$i]['requestId'] = $row['requestId'];
                    $data[$i]['code'] = $row['code'];
                    $data[$i]['codeName'] = $code->getName();
                    $data[$i]['startDate'] = $row['startDate'];
                    $data[$i]['endDate'] = $row['endDate'];
                    $data[$i]['totalHoursRequested'] = $row['totalHoursRequested'];
                    $data[$i]['dateOfRequest'] = $row['dateOfRequest'];
                    $data[$i]['note'] = $row['note'];
                    $data[$i]['dateTimes'] = json_decode($row['dateTimes']);
                    $data[$i]['status'] = $row['status'];
                    $data[$i]['superNote'] = $row['superNote'];
                    $i++;
                }
            }
        }

        return $data;
    }

    function getAll()
    {
        $user = new userModel();
        $userId = $user->getId();

        $date = new DateTime();
        $date->modify("-4 weeks");
        $expireDate = $date->format('Y-m-d');

        $sql = "
          SELECT * FROM requests 
          WHERE userId = '".$userId."'
          AND dateOfRequest >= '".$this->db->real_escape_string($expireDate)."'
          ORDER BY status ASC, dateOfRequest DESC
        ";
        $result = $this->db->query($sql);

        $code = new codeModel();
        $data = array();
        $i = 0;
        while($row = $result->fetch_assoc())
        {
            $code->loadRequestCode($row['code']);
            $data[$i]['id'] = $row['id'];
            $data[$i]['requestId'] = $row['requestId'];
            $data[$i]['code'] = $row['code'];
            $data[$i]['codeName'] = $code->getName();
            $data[$i]['startDate'] = $row['startDate'];
            $data[$i]['endDate'] = $row['endDate'];
            $data[$i]['totalHoursRequested'] = $row['totalHoursRequested'];
            $data[$i]['dateOfRequest'] = $row['dateOfRequest'];
            $data[$i]['note'] = $row['note'];
            $data[$i]['status'] = $row['status'];
            $supervisor = new userModel();
            $supervisorData = $supervisor->getById($row['approvedById']);
            $data[$i]['approvedById'] = $row['approvedById'];
            $data[$i]['approvedByName'] = $supervisorData;
            $data[$i]['dateTimes'] = json_decode($row['dateTimes']);
            $data[$i]['superNote'] = $row['superNote'];
            $i++;
        }

        return $data;
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
        $this->setNote($newDateTimes['note']);
        $this->setTotalHoursRequested($newDateTimes['totalHoursRequested']);
        $this->setDateOfRequest(date('Y-m-d'));
        $this->setStatus(3);
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
                (requestId, code, userId, startDate, endDate, dateTimes, note, totalHoursRequested, status) 
                VALUES
                ('".$this->db->real_escape_string($this->requestId)."',
                '".$this->db->real_escape_string($this->code)."',
                '".$this->db->real_escape_string($userId)."',
                '".$this->db->real_escape_string($this->startDate)."',
                '".$this->db->real_escape_string($this->endDate)."',
                '".$this->db->real_escape_string($this->dateTimes)."',
                '".$this->db->real_escape_string($this->note)."',
                '".$this->db->real_escape_string($this->totalHoursRequested)."',
                '".$this->db->real_escape_string($this->status)."'
                )";

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
        $this->load($requestId);
        $superUser = new userModel();
        //$email = $superUser->userSupervisor()."@twinfallspubliclibrary.org";
        $email = "tbartley@twinfallspubliclibrary.org";
        $user = new userModel();
        $userInfo = $user->userInfo($this->userId);
        $msg = $userInfo['firstName']." ".$userInfo['lastName']." has requested time off for the following:\r\n\r\n";
        $msg .= "Code: ".$this->codeName."\r\n";

        if($this->startDate == $this->endDate)
        {
            $msg .= "Date: ".$this->startDate;
        }
        else
        {
            $msg .= "Dates: ".$this->startDate." - ".$this->endDate;
        }

        $msg .= "\r\nTotal Hours Requested: ".$this->totalHoursRequested;
        $msg .= "\r\n\r\nPlease login to http://devtimetracker to review.";
        $headers = "";
        $headers .= "From: TFPL TimeTracker <noreply@tfpl.org> \r\n";
        mail($email, "New TimeTracker Request",$msg,$headers);
    }

    function remove($requestId)
    {
        $this->load($requestId);
        $uid = $this->getUserId();
        $user = new userModel();
        $id = $user->getId();
        $status = $this->getStatus();
        if($id == $uid && $status == 0)
        {
            $sql = "DELETE FROM requests WHERE requestId = '".$this->db->real_escape_string($requestId)."'";
            if($this->db->query($sql))
            {
                return true;
            }
        }
    }

    function changeToPendingApproval($requestId)
    {
        $sql = "UPDATE requests SET status = '0' WHERE requestId = '".$this->db->real_escape_string($requestId)."'";
        if($this->db->query($sql))
        {
            return true;
        }
    }

    function approve($requestId)
    {
        $user = new userModel();
        $request = new requestModel();
        $request->load($requestId);

        if($user->getAuthLevel() == 500 || $user->getAuthLevel() == 900)
        {
            $staff = $user->getById($request->getUserId());
            //If the current user is the supervisor of the staff request or an admin
            if($staff['supervisorId'] == $user->getId() || $user->getAuthLevel() == 900)
            {
                $sql = "UPDATE requests SET status = '1', approvedById = '".$this->db->real_escape_string($user->getId())."' WHERE requestId = '".$this->db->real_escape_string($requestId)."'";
                if($this->db->query($sql))
                {
                    //Log to audit
                    $audit = new auditModel();
                    $audit->setUserId($staff['id']);
                    $audit->setAction('Request Approved');
                    $audit->setItem($user->getUsername()." approved ".$request->getCodeName()." for ".$staff['firstName']." ".$staff['lastName']." for the following dates ".$request->getStartDate()." through ".$request->getEndDate().". Request ID:".$request->getRequestId()." ");
                    $audit->save();
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
    }

    function cancel($requestId)
    {
        $user = new userModel();
        $request = new requestModel();
        $request->load($requestId);

        if($user->getAuthLevel() == 500 || $user->getAuthLevel() == 900)
        {
            $staff = $user->getById($request->getUserId());
            //If the current user is the supervisor of the staff request or an admin
            if($staff['supervisorId'] == $user->getId() || $user->getAuthLevel() == 900)
            {
                $sql = "UPDATE requests SET status = '4', approvedById = '".$this->db->real_escape_string($user->getId())."' WHERE requestId = '".$this->db->real_escape_string($requestId)."'";
                if($this->db->query($sql))
                {
                    //Log to audit
                    $audit = new auditModel();
                    $audit->setUserId($staff['id']);
                    $audit->setAction('Request Cancelled');
                    $audit->setItem($user->getUsername()." cancelled ".$request->getCodeName()." for ".$staff['firstName']." ".$staff['lastName']." for the following dates ".$request->getStartDate()." through ".$request->getEndDate().". Request ID:".$request->getRequestId()." ");
                    $audit->save();
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
    }

    function hasAccess($requestId)
    {
        //Currently logged in user
        $user = new userModel();
        $userId = $user->getId();

        //Load request
        $request = new requestModel();
        $request->load($requestId);

        //If current user id == request userId
        if($userId == $request->getUserId())
        {
            return true;
        }
        else
        {
            //Check if the current user is the requesters supervisor
            // RequesterId
            $requesterId = $request->userId."<br>";

            // RequesterSupervisor ID
            $supervisor = new userModel();
            $superInfo = $supervisor->userInfo($requesterId);

            // Check if current user ID == RequesterSupervisor ID
            if($userId == $superInfo['supervisorId'])
            {
                return true;
            }
            else
            {
                //Check if the current user is an administrator
                if($user->getAuthLevel() == 900)
                {
                    return true;
                }
            }
        }
    }

    function decline($requestId,$note)
    {
        $user = new userModel();
        $request = new requestModel();
        $request->load($requestId);

        if($user->getAuthLevel() == 500 || $user->getAuthLevel() == 900)
        {
            $staff = $user->getById($request->getUserId());
            //If the current user is the supervisor of the staff request or an admin
            if($staff['supervisorId'] == $user->getId() || $user->getAuthLevel() == 900)
            {
                $sql = "UPDATE requests SET status = '2', superNote = '".$this->db->real_escape_string($note)."', approvedById = '".$this->db->real_escape_string($user->getId())."' WHERE requestId = '".$this->db->real_escape_string($requestId)."'";
                if($this->db->query($sql))
                {
                    //Log to audit
                    $audit = new auditModel();
                    $audit->setUserId($staff['id']);
                    $audit->setAction('Request Declined');
                    $audit->setItem($user->getUsername()." declined ".$request->getCodeName()." for ".$staff['firstName']." ".$staff['lastName']." for the following dates ".$request->getStartDate()." through ".$request->getEndDate().". Request ID:".$request->getRequestId()." ");
                    $audit->save();
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
    }
}