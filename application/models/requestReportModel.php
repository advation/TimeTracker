<?php
class requestReportModel extends Staple_Model
{
    private $db;

    function __construct()
    {
        $this->db = Staple_DB::get();
    }

    function staffRequests($startDate = null, $endDate = null, $userId = null)
    {
        //Set start and end dates for the pay period
        $date = new dateTime();
        if($startDate == null)
        {
            $date->modify('last month');
            $date->modify('first day of this month');
            $date->modify("+25 days");
            $date->setTime(0,0,0);
            $startDate = $date->format("m/d/Y");
        }

        if($endDate == null)
        {
            $date->modify('next month');
            $date->modify("first day of this month");
            $date->modify("+23 days");
            $date->setTime(24,00,00);
            $endDate = $date->format("m/d/Y");
        }

        $user = new userModel();

        //Build data array
        $data = array();

        if($userId == null)
        {
            if(count($user->assignedUsers()) > 0)
            {
                foreach($user->assignedUsers() as $user)
                {
                    $sql = "SELECT code, SUM(totalHoursRequested) as totalHours FROM requests WHERE startDate BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND userId = '" . $user['id'] . "' AND status = '1' GROUP BY code ASC";

                    $query = $this->db->query($sql);
                    $userInfo = array();

                    if ($query->num_rows > 0)
                    {
                        $userInfo['user'] = $user;

                        while ($row = $query->fetch_assoc())
                        {
                            $codeArray = array();
                            $code = new codeModel();
                            $code->loadRequestCode($row['code']);
                            $codeArray['code'] = $row['code'];
                            $codeArray['codeName'] = $code->getName();
                            $codeArray['total'] = $row['totalHours'];
                            $userInfo['hours'][] = $codeArray;
                            $userInfo['payperiod']['startDate'] = $startDate;
                            $userInfo['payperiod']['endDate'] = $endDate;
                        }
                        $data[] = $userInfo;
                    }
                }
            }
        }
        else
        {
            $sql = "SELECT code, SUM(totalHoursRequested) as totalHours FROM requests WHERE startDate BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND userId = '" . $userId . "' AND status = '1' GROUP BY code ASC";

            $query = $this->db->query($sql);
            $userInfo = array();

            if ($query->num_rows > 0) {
                $userInfo['user'] = $user;

                while ($row = $query->fetch_assoc()) {
                    $codeArray = array();
                    $code = new codeModel();
                    $code->loadRequestCode($row['code']);
                    $codeArray['code'] = $row['code'];
                    $codeArray['codeName'] = $code->getName();
                    $codeArray['total'] = $row['totalHours'];
                    $userInfo['hours'][] = $codeArray;
                    $userInfo['payperiod']['startDate'] = $startDate;
                    $userInfo['payperiod']['endDate'] = $endDate;
                }
                $data[] = $userInfo;
            }
        }

        return $data;
    }

    function userUpComingRequests()
    {
        //Get a list of all active users
        $user = new userModel();
        $userId = $user->getId();

        $date = new DateTime();
        $date->modify("-1 year");
        $date->setTime(0,0,0);
        $startDate = $date->format("m/d/Y");
        $date->modify("+1 year");
        $date->setTime(0,0,0);
        $endDate = $date->format("m/d/Y");

        //Build data array
        $data = array();
        $sql = "SELECT code, SUM(totalHoursRequested) as totalHours FROM requests WHERE userId = '".$userId."' AND startDate BETWEEN '".$startDate."' AND '".$endDate."' GROUP BY code ASC";
        $query  = $this->db->query($sql);
        $userInfo = array();

        if($query->num_rows > 0)
        {
            while($row = $query->fetch_assoc())
            {
                $codeArray = array();
                $code = new codeModel();
                $code->loadRequestCode($row['code']);
                $codeArray['code'] = $row['code'];
                $codeArray['codeName'] = $code->getName();
                $codeArray['total'] = $row['totalHours'];
                $userInfo['hours'][] = $codeArray;
                $userInfo['payperiod']['startDate'] = $startDate;
                $userInfo['payperiod']['endDate'] = $endDate;
            }
            $data[] = $userInfo;
        }


        return $data;
    }

    function userRequests($startDate, $endDate, $userId)
    {
        //Get a list of all active users
        $user = new userModel();
        $user = $user->getById($userId);

        //Build data array
        $data = array();

        $sql = "SELECT code, SUM(totalHoursRequested) as totalHours FROM requests WHERE startDate BETWEEN '".$startDate."' AND '".$endDate."' AND userId = '".$user['id']."' AND status = '1' GROUP BY code ASC";
        $query  = $this->db->query($sql);
        $userInfo = array();

        if($query->num_rows > 0)
        {
            $userInfo['user'] = $user;

            while($row = $query->fetch_assoc())
            {
                $codeArray = array();
                $code = new codeModel();
                $code->loadRequestCode($row['code']);
                $codeArray['code'] = $row['code'];
                $codeArray['codeName'] = $code->getName();
                $codeArray['total'] = $row['totalHours'];
                $userInfo['hours'][] = $codeArray;
                $userInfo['payperiod']['startDate'] = $startDate;
                $userInfo['payperiod']['endDate'] = $endDate;
            }
            $data[] = $userInfo;
        }


        return $data;
    }

    function dateRangeRequests($startDate, $endDate)
    {
        //Get a list of all active users
        $users = new userModel();
        $users = $users->listActive();

        //Build data array
        $data = array();

        foreach($users as $user)
        {
            /*$sql = "
            SELECT code, SUM(totalHoursRequested) as totalHours FROM requests 
            WHERE startDate 
            BETWEEN '".$startDate."' 
            AND '".$endDate."' 
            AND userId = '".$user['id']."' 
            AND status = '1' 
            GROUP BY code ASC";*/

            $sql = "
            SELECT code, SUM(totalHoursRequested) as totalHours FROM requests 
            WHERE STR_TO_DATE(startDate, '%m/%d/%Y') 
            BETWEEN STR_TO_DATE('$startDate', '%m/%d/%Y')
            AND STR_TO_DATE('$endDate', '%m/%d/%Y')
            AND userId = '".$user['id']."' 
            AND status = '1' 
            GROUP BY code ASC
            ";

            $query  = $this->db->query($sql);
            $userInfo = array();

            if($query->num_rows > 0)
            {
                $userInfo['user'] = $user;

                while($row = $query->fetch_assoc())
                {
                    $codeArray = array();
                    $code = new codeModel();
                    $code->loadRequestCode($row['code']);
                    $codeArray['code'] = $row['code'];
                    $codeArray['codeName'] = $code->getName();
                    $codeArray['total'] = $row['totalHours'];
                    $userInfo['hours'][] = $codeArray;
                    $userInfo['payperiod']['startDate'] = $startDate;
                    $userInfo['payperiod']['endDate'] = $endDate;
                }
                $data[] = $userInfo;
            }
        }

        return $data;
    }

    function currentPayperiodRequests()
    {
        //Set start and end dates for the pay period
        $date = new dateTime();

        $date->modify('last month');
        $date->modify('first day of this month');
        $date->modify("+25 days");
        $date->setTime(0,0,0);
        $startDate = $date->format("m/d/Y");

        $date->modify('next month');
        $date->modify("first day of this month");
        $date->modify("+23 days");
        $date->setTime(24,00,00);
        $endDate = $date->format("m/d/Y");

        //Get a list of all active users
        $users = new userModel();
        $users = $users->listActive();

        //Build data array
        $data = array();

        foreach($users as $user)
        {
            $sql = "SELECT code, SUM(totalHoursRequested) as totalHours FROM requests WHERE startDate BETWEEN '".$startDate."' AND '".$endDate."' AND userId = '".$user['id']."' AND status = '1' GROUP BY code ASC";
            $query  = $this->db->query($sql);
            $userInfo = array();

            if($query->num_rows > 0)
            {
                $userInfo['user'] = $user;

                while($row = $query->fetch_assoc())
                {
                    $codeArray = array();
                    $code = new codeModel();
                    $code->loadRequestCode($row['code']);
                    $codeArray['code'] = $row['code'];
                    $codeArray['codeName'] = $code->getName();
                    $codeArray['total'] = $row['totalHours'];
                    $userInfo['hours'][] = $codeArray;
                    $userInfo['payperiod']['startDate'] = $startDate;
                    $userInfo['payperiod']['endDate'] = $endDate;
                }
                $data[] = $userInfo;
            }
        }

        return $data;
    }
}