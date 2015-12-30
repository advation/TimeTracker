<?php
class reviewModel extends Staple_Model
{
    private $db;

    private $id;
    private $accountId;
    private $payPeriodMonth;
    private $payPeriodYear;
    private $supervisorId;

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

    function __construct($year, $month, $uid)
    {
        $this->db = Staple_DB::get();

        echo $uid;
    }
}
?>