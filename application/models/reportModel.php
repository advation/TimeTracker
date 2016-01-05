<?php
class reportModel extends Staple_Model
{
    private $db;
    private $timesheets;

    /**
     * @return array
     */
    public function getTimesheets()
    {
        return $this->timesheets;
    }

    /**
     * @param array $timesheets
     */
    public function setTimesheets($timesheets)
    {
        $this->timesheets = $timesheets;
    }

    function __construct($year, $month, $inactive = null)
    {
        $this->db = Staple_DB::get();

        if($inactive != null)
        {
            $staffIds = $this->getStaffIds(1);
        }
        else
        {
            $staffIds = $this->getStaffIds();
        }

        $data = array();

        if(count($staffIds) > 0)
        {
            foreach($staffIds as $key => $value)
            {
                $data[$value] = $this->getTimesheet($key, $year, $month);
                $data[$value]['id'] = $key;
            }
        }

        $this->timesheets = $data;
    }

    function getStaffIds($inactive = null)
    {
        $auth = Staple_Auth::get();
        $user = new userModel($auth->getAuthId());
        $userId = $user->getId();
        $authLevel = $user->getAuthLevel();

        $data = array();

        if($authLevel >= 900)
        {
            if($inactive == 1)
            {
                $sql = "
                SELECT id, firstName, lastName FROM accounts WHERE status = 0 ORDER BY lastName ASC
                ";
            }
            else
            {
                $sql = "
                SELECT id, firstName, lastName FROM accounts WHERE status = 1 ORDER BY lastName ASC
                ";
            }
        }
        else
        {
            if($inactive == 1)
            {
                $sql = "
                SELECT id, firstName, lastName FROM accounts WHERE status = 0 AND supervisorId = '" . $this->db->real_escape_string($userId) . "' ORDER BY lastName ASC
                ";
            }
            else
            {
                $sql = "
                SELECT id, firstName, lastName FROM accounts WHERE status = 1 AND supervisorId = '" . $this->db->real_escape_string($userId) . "' ORDER BY lastName ASC
                ";
            }
        }

        $query = $this->db->query($sql);

        while($result = $query->fetch_assoc())
        {
            $data[$result['id']] = $result['lastName'].", ".$result['firstName'];
        }
        return $data;
    }

    function getTimesheet($userId, $year, $month)
    {
        $currentDate = new DateTime();
        $currentDate->setDate($year, $month, 1);

        $currentYear = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');
        $currentMonthText = $currentDate->format('F');
        $startDate = $currentDate->modify('-1 month +25 day')->format('Y-m-d');
        $startDateTimeString = strtotime($startDate);
        $currentDate->setDate($year, $month, 1);
        $endDate = $currentDate->modify('+25 day')->format('Y-m-d');
        $endDateTimeString = strtotime($endDate);

        $sql = "
            SELECT id FROM timeEntries WHERE inTime > $startDateTimeString AND inTime < $endDateTimeString AND userId = $userId ORDER BY inTime ASC;
        ";

        $data = array();

        $query = $this->db->query($sql);

        while($result = $query->fetch_assoc())
        {
            $data[$result['id']] = $this->calculateEntry($result['id']);
        }

        return $data;
    }

    function calculateEntry($id)
    {
        $sql = "
            SELECT * FROM timeEntries WHERE id = '".$this->db->real_escape_string($id)."';
        ";

        $query = $this->db->query($sql);
        $result = $query->fetch_assoc();

        //Set inTime
        $inTime = new DateTime();
        $inTime->setTimestamp($result['inTime']);
        $roundedInTime = $this->nearestQuarterHour($result['inTime']);
        $inTimeRaw = $result['inTime'];
        $inTimeDate = date("Y-m-d", $result['inTime']);

        //Out Time
        $outTime = new DateTime();
        $outTime->setTimestamp($result['outTime']);
        $roundedOutTime = $this->nearestQuarterHour($result['outTime']);
        $outTimeRaw = $result['outTime'];
        $roundedOutTime = $this->nearestQuarterHour($result['outTime']);
        $outTimeDate = date("Y-m-d", $result['outTime']);

        $lessTime = $result['lessTime'];
        $timestamp = $result['timestamp'];
        $note = $result['note'];

        //Calculate Time Worked
        switch($result['lessTime'])
        {
            case 60:
                $lessTime = 1;
                break;
            case 30:
                $lessTime = 0.5;
                break;
            case 15:
                $lessTime = 0.25;
                break;
            default:
                $lessTime = 0;
        }

        //Total Worked Time
        $dateTime1 = new DateTime($roundedInTime);
        $dateTime1->setDate(date('Y',strtotime($inTimeDate)), date('m',strtotime($inTimeDate)), date('d',strtotime($inTimeDate)));
        $dateTime2 = new DateTime($roundedOutTime);
        $dateTime2->setDate(date('Y',strtotime($outTimeDate)), date('m',strtotime($outTimeDate)), date('d',strtotime($outTimeDate)));
        $interval = $dateTime1->diff($dateTime2);

        $timeWorked = $this->timeToDecimal($interval->h.":".$interval->i)-$lessTime;

        if($timeWorked !== 0)
        {
            $timeWorked = $timeWorked;
        }
        else
        {
            $timeWorked = 0;
        }

        //Get Code Information
        $code = new codeModel();
        $codeId = $result['codeId'];
        $code->load($result['codeId']);
        $codeName = $code->getName();

        $data['date'] = date('Y-m-d', $inTimeRaw);
        $data['inTime'] = $inTimeRaw;
        $data['outTime'] = $outTimeRaw;
        $data['lessTime'] = $lessTime;
        $data['timeWorked'] = $timeWorked;
        $data['code'] = $codeName;
        $data['timestamp'] = $timestamp;
        $data['note'] = $note;

        //Get the user of the entry.
        $entry = new timeEntryModel($id);

        if($entry->validated($id,$result['userId']))
        {
            $data['validated'] = 0;
        }
        else
        {
            $data['validated'] = 1;
        }

        return $data;
    }

    function nearestQuarterHour($time)
    {
        //$time = strtotime($time);
        $round = 15*60;
        $rounded = round($time/$round)*$round;

        return date("g:i A", $rounded);
    }

    function timeToDecimal($time)
    {
        $timeArr = explode(':', $time);
        $hours = $timeArr[0]*1;
        $minutes = $timeArr[1]/60;
        $dec = $hours + $minutes;

        if($dec > 0)
        {
            return round($dec,2);
        }
        else
        {
            return 0;
        }
    }

    function weekly()
    {

    }

    function payPeriodTotals($year, $month)
    {
        //Get all users
        $users = new userModel();
        $accounts = $users->listAll();

        $data = array();

        $date = new DateTime();
        $date->setTime(0,0,0);
        $date->setDate($year,$month,26);
        $date->modify('-1 month');
        $startDate = $date->format('U');
        $date->modify('+1 month -1 day');
        $date->setTime(23,59,59);
        $endDate = $date->format('U');

        foreach($accounts as $account)
        {
            $userId = $account['id'];
            $userName = $account['lastName'].", ".$account['firstName'];
            $sql = "
                SELECT * FROM timeEntries WHERE inTime >= '".$this->db->real_escape_string($startDate)."' AND inTime <= '".$this->db->real_escape_string($endDate)."' AND userId = '".$this->db->real_escape_string($userId)."' ORDER BY inTime ASC;
            ";

            $query = $this->db->query($sql);
            $data2 = array();

            $setDate = 0;
            $timeWorked = 0;
            while($result = $query->fetch_assoc())
            {
                $day = new DateTime();
                $day->setTimestamp($result['inTime']);
                $date = $day->format('Y-m-d');
                $entry = $this->calculateEntry($result['id']);

                if($date == $setDate)
                {
                    $timeWorked = $timeWorked + $entry['timeWorked'];
                }
                else
                {
                    $timeWorked = $entry['timeWorked'];
                }

                $data2[$date] = $timeWorked;
                $setDate = $date;
            }
            $data[$userName] = $data2;
        }
        return $data;
    }

    function payroll($year, $month)
    {
        $users = new userModel();
        $accounts = $users->listAll();

        $data = array();

        $date = new DateTime();
        $date->setTime(0,0,0);
        $date->setDate($year,$month,26);
        $date->modify('-1 month');

        $startDate = $date->format('U');
        $date->modify('+1 month -1 day');
        $date->setTime(23,59,59);

        $endDate = $date->format('U');

        foreach($accounts as $account)
        {
            $userId = $account['id'];
            $userName = $account['lastName'] . ", " . $account['firstName'];
            $sql = "
                SELECT * FROM timeEntries WHERE inTime >= '" . $this->db->real_escape_string($startDate) . "' AND inTime <= '" . $this->db->real_escape_string($endDate) . "' AND userId = '" . $this->db->real_escape_string($userId) . "' ORDER BY inTime ASC;
            ";

            $query = $this->db->query($sql);
            $data2 = array();

            $setCode = 0;
            $timeWorked = 0;
            while($result = $query->fetch_assoc())
            {
                $entry = $this->calculateEntry($result['id']);
                $code = $entry['code'];

                if($code == $setCode)
                {
                    $timeWorked = $timeWorked + $entry['timeWorked'];
                }
                else
                {
                    $timeWorked = $entry['timeWorked'];
                }

                $data2[$code] = $timeWorked;
                $setCode = $code;
            }
            $data[$userName] = $data2;
        }
        return $data;
    }
}