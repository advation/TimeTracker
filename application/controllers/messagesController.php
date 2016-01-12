<?php

class messagesController extends Staple_Controller
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
        $form = new newMessageForm();

        if($form->wasSubmitted())
        {
            $form->addData($_POST);
            if($form->validate())
            {
                $data = $form->exportFormData();

                $message = new messagesModel();
                $message->setMessage($data['message']);
                $message->setExpireDate($data['expireDate']);
                $message->save();

                $form = new newMessageForm();
                $this->view->form = $form;

            }
            else
            {
                $this->view->form = $form;
                $this->layout->addScriptBlock('$(document).ready(function() { $("#newMessage").foundation("reveal", "open"); }); ');
            }
        }
        else
        {
            $this->view->form = $form;
        }

        $messages = new messagesModel();
        $this->view->messages = $messages->getMessages();
    }

    public function edit($id = null)
    {
        if($id != null)
        {
            $form = new editMessageForm();
            $message = new messagesModel();

            $message->load($id);

            $this->view->id = $message->getId();

            $data['id'] = $message->getId();
            $data['message'] = $message->getMessage();
            $data['expireDate'] = $message->getExpireDate();

            $form->setAction($this->_link(array('messages','edit',$message->getId())));
            $form->addData($data);

            if($form->wasSubmitted())
            {
                $form->addData($_POST);
                if($form->validate())
                {
                    $data = $form->exportFormData();

                    $message = new messagesModel();
                    $message->setId($id);
                    $message->setMessage($data['message']);
                    $message->setExpireDate($data['expireDate']);
                    $message->save();
                    header("location:".$this->_link(array('messages'))."");
                }
                else
                {
                    $this->view->form = $form;
                }
            }
            else
            {
                $this->view->form = $form;
            }
        }
        else
        {
            header("location: ".$this->_link(array('messages'))."");
        }
    }

    public function delete($id)
    {
        $message = new messagesModel();
        $message->delete($id);
        header("location:".$this->_link(array('messages'))."");
    }
}

?>