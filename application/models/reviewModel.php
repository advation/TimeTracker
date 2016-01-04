<?php
class reviewModel extends Staple_Model
{
    private $db;

    private $accountId;
    private $payPeriodMonth;
    private $payPeriodYear;
    private $supervisorId;
    private $supervisorFirstName;
    private $supervisorLastName;
    private $reviewDate;

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return mixed
     */
    public function getPayPeriodMonth()
    {
        return $this->payPeriodMonth;
    }

    /**
     * @param mixed $payPeriodMonth
     */
    public function setPayPeriodMonth($payPeriodMonth)
    {
        $this->payPeriodMonth = $payPeriodMonth;
    }

    /**
     * @return mixed
     */
    public function getPayPeriodYear()
    {
        return $this->payPeriodYear;
    }

    /**
     * @param mixed $payPeriodYear
     */
    public function setPayPeriodYear($payPeriodYear)
    {
        $this->payPeriodYear = $payPeriodYear;
    }

    /**
     * @return mixed
     */
    public function getSupervisorId()
    {
        return $this->supervisorId;
    }

    /**
     * @param mixed $supervisorId
     */
    public function setSupervisorId($supervisorId)
    {
        $this->supervisorId = $supervisorId;
    }

    /**
     * @return mixed
     */
    public function getReviewDate()
    {
        return $this->reviewDate;
    }

    /**
     * @param mixed $reviewDate
     */
    public function setReviewDate($reviewDate)
    {
        $this->reviewDate = $reviewDate;
    }

    /**
     * @return mixed
     */
    public function getSupervisorFirstName()
    {
        return $this->supervisorFirstName;
    }

    /**
     * @param mixed $supervisorFirstName
     */
    public function setSupervisorFirstName($supervisorFirstName)
    {
        $this->supervisorFirstName = $supervisorFirstName;
    }

    /**
     * @return mixed
     */
    public function getSupervisorLastName()
    {
        return $this->supervisorLastName;
    }

    /**
     * @param mixed $supervisorLastName
     */
    public function setSupervisorLastName($supervisorLastName)
    {
        $this->supervisorLastName = $supervisorLastName;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();
    }

    function load($year, $month)
    {
        $data = array();
        $sql = "SELECT * FROM timesheetReview WHERE payPeriodYear = '".$this->db->real_escape_string($year)."' AND payPeriodMonth = '".$this->db->real_escape_string($month)."'";

        $query = $this->db->query($sql);

        if($query->num_rows > 0)
        {
            while($result = $query->fetch_assoc())
            {
                $user = new userModel();
                $account = $user->userInfo($result['accountId']);
                $data[$account['lastName'].", ".$account['firstName']] = $result;

                $user2 = new userModel();
                $account2 = $user2->userInfo($result['supervisorId']);

                $date = new DateTime();
                $date->setTimestamp(strtotime($result['reviewDate']));
                $data[$account['lastName'].", ".$account['firstName']]['reviewDateFormatted'] = $date->format('F jS Y');

                $data[$account['lastName'].", ".$account['firstName']]['supervisor'] = $account2['firstName']." ".$account2['lastName'];
            }
        }
        return $data;
    }

    function save()
    {
        if(isset($this->accountId) && isset($this->payPeriodYear) && isset($this->payPeriodMonth))
        {
            //Get current users ID.
            $user = new userModel();
            $supervisorId = $user->getId();

            //Check if entry already exists
            $sql = "
                SELECT id FROM timesheetReview WHERE accountId = '".$this->db->real_escape_string($this->accountId)."' AND payPeriodMonth = '".$this->db->real_escape_string($this->payPeriodMonth)."' AND payPeriodYear = '".$this->db->real_escape_string($this->payPeriodYear)."';
            ";

            $result = $this->db->query($sql)->num_rows;
            if($result == 0)
            {
                $sql = "
                    INSERT INTO timesheetReview (accountId, payPeriodMonth, payPeriodYear, supervisorId) VALUES ('".$this->db->real_escape_string($this->accountId)."','".$this->db->real_escape_string($this->payPeriodMonth)."','".$this->db->real_escape_string($this->payPeriodYear)."','".$this->db->real_escape_string($supervisorId)."')
                ";

                if($this->db->query($sql))
                {
                    return true;
                }
            }
        }
    }

    function remove($userId, $year, $month)
    {
        $sql = "DELETE FROM timesheetReview WHERE accountId = '".$userId."' AND  payPeriodMonth = '".$month."' AND payPeriodYear = '".$year."';";

        if($this->db->query($sql))
        {
            return true;
        }
    }
}