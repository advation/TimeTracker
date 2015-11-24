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
        if(array_key_exists('items',$_GET))
        {
            $_SESSION['items'] = $_GET['items'];
        }

        if(array_key_exists('items',$_SESSION))
        {
            $items = $_SESSION['items'];
        }
        else
        {
            $items = 20;
        }

        if(array_key_exists('page',$_GET))
        {
            $page = $_GET['page'];
        }
        else
        {
            $page = 1;
        }

        $audit = new auditModel();

        $auditLog = $audit->getAll($page,$items);
        $this->view->audit = $auditLog;

        $this->view->pager = $audit->getPager();
    }
}

?>