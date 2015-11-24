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
        if(array_key_exists('page',$_GET))
        {
            $page = $_GET['page'];
        }
        else
        {
            $page = 1;
        }

        if(array_key_exists('items',$_GET))
        {
            Staple_Registry::set('items',$_GET['items']);
        }

        if(Staple_Auth::get('items') == null)
        {
            $items = 20;
        }
        else
        {
            $items = Staple_Registry::get('items');
        }

        $audit = new auditModel();

        $auditLog = $audit->getAll($page,$items);
        $this->view->audit = $auditLog;

        $this->view->pager = $audit->getPager();
    }
}

?>