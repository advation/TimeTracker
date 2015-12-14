<?php
class weeklyReportModel extends Staple_Model
{
    private $db;

    function __construct()
    {
        $this->db = Staple_DB::get();
    }

    function timeWorked($uid,$year)
    {
        //Get an array of weeks
        $weeks = array();
        for($i=1;$i<53;$i++)
        {
            $weeks[$i] = $this->getStartAndEndDate($i, $year);

            $sql = "
              SELECT ROUND((TIME_TO_SEC(SEC_TO_TIME(SUM(outTime - inTime)-SUM(lessTime*60)))/3600)*4)/4 AS 'totalTime' FROM timeEntries WHERE inTime >= ".$weeks[$i]['start']['unix']." AND outTime <= ".$weeks[$i]['end']['unix']." AND userId = $uid;
            ";

            $total = 0;
            if($this->db->query($sql)->num_rows > 0)
            {
                $query = $this->db->query($sql);
                $result = $query->fetch_assoc();
                $total = $result['totalTime'];
            }
            $weeks[$i]['hoursWorked'] = $total;
        }

        return $weeks;
    }

    function getWeekWorked($uid,$week,$year)
    {
        $week = $this->getStartAndEndDate($week, $year);

        $sql = "
              SELECT ROUND((TIME_TO_SEC(SEC_TO_TIME(SUM(outTime - inTime)-SUM(lessTime*60)))/3600)*4)/4 AS 'totalTime' FROM timeEntries WHERE inTime >= ".$week['start']['unix']." AND outTime <= ".$week['end']['unix']." AND userId = $uid;
            ";

        $total = 0;
        if($this->db->query($sql)->num_rows > 0)
        {
            $query = $this->db->query($sql);
            $result = $query->fetch_assoc();
            $total = $result['totalTime'];
        }

        if($total == 0)
        {
            $total = "0";
        }

        $week['total'] = $total;
        return $week;
    }

    function getStartAndEndDate($week, $year)
    {
        $dto = new DateTime();
        $dto->setISODate($year, $week,0);

        $ret = array();
        $ret['week'] = $week;
        $ret['year'] = $year;

        //Week Start
        $dto->setTime(0,0,0);
        $ret['start']['unix'] = $dto->format('U');
        $ret['start']['formatted'] = $dto->format('Y-m-d');
        $ret['start']['dayName'] = $dto->format('l');
        $ret['start']['day'] = $dto->format('jS');
        $ret['start']['month'] = $dto->format('F');
        $ret['start']['year'] = $dto->format('Y');

        //Week End
        $dto->modify('+6 days')->setTime(23,59,59);
        $ret['end']['unix'] = $dto->format('U');
        $ret['end']['formatted'] = $dto->format('Y-m-d');
        $ret['end']['dayName'] = $dto->format('l');
        $ret['end']['day'] = $dto->format('jS');
        $ret['end']['month'] = $dto->format('F');
        $ret['end']['year'] = $dto->format('Y');

        return $ret;
    }
}