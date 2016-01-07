<?php
class calculateModel extends Staple_Model
{
    private $db;

    function __construct()
    {
        $this->db = Staple_DB::get();
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

    function calculatedTotals($uid,$startDate,$endDate)
    {
        $sql = "SELECT ROUND((TIME_TO_SEC(SEC_TO_TIME(SUM(outTime - inTime)-SUM(lessTime*60)))/3600)*4)/4 AS 'totalTime' FROM timeEntries WHERE inTime >= $startDate AND outTime <= $endDate AND userId = $uid";

        if($this->db->query($sql)->num_rows > 0)
        {
            $query = $this->db->query($sql);
            $result = $query->fetch_assoc();
            return round($result['totalTime'],2);
        }
        else
        {
            return 0;
        }
    }

    function calculatedCodeTotals($uid,$startDate,$endDate)
    {
        $codes = new codeModel();
        $codes = $codes->allCodes();

        $data = array();

        foreach($codes as $codeKey=>$codeName)
        {
            $sql = "SELECT ROUND((TIME_TO_SEC(SEC_TO_TIME(SUM(outTime - inTime)-SUM(lessTime*60)))/3600)*4)/4 AS 'totalTime' FROM timeEntries WHERE inTime >= $startDate AND outTime <= $endDate AND userId = $uid AND codeId = $codeKey";

            if($this->db->query($sql)->num_rows > 0)
            {
                $query = $this->db->query($sql);
                $result = $query->fetch_assoc();
                $data[$codeKey] = round($result['totalTime'],2);
            }
        }
        return $data;
    }
}