<?php

class auditController extends Staple_Controller
{
    public function _start()
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();
        if($this->authLevel < 900)
        {
            header("location:".$this->_link(array('index','index'))."");
        }
    }

    public function index()
    {
        $audit = new auditModel();
        $auditLog = $audit->getAll();

        $this->view->audit = $auditLog;
    }
}

?>