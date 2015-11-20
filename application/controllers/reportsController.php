<?php

class reportsController extends Staple_Controller
{
    private $authLevel;

    public function _start()
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();
        if($this->authLevel < 500)
        {
            header("location:".$this->_link(array('index','index'))."");
        }
    }

    public function index($year = null, $month = null)
    {
        if($year == null)
        {
            $year = date('Y');
        }

        if($month == null)
        {
            $month = date('m');
        }

        $report = new reportModel($year,$month);

    }
}