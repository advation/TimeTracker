<?php

class messagesController extends Staple_Controller
{
    public function _start()
    {
        $auth = Staple_Auth::get();
        $this->authLevel = $auth->getAuthLevel();
        if($this->authLevel < 500)
        {
            header("location:".$this->_link(array('index','index'))."");
        }
    }

    public function index()
    {
        $user = new userModel();
        if($user->getAuthLevel() >= 900)
        {
            $form = new newMessageForm();

            if($form->wasSubmitted())
            {
                $form->addData($_POST);
                if($form->validate())
                {
                    $data = $form->exportFormData();

                    if($data['account'] == 'all')
                    {
                        $message = new messageModel();
                        $message->setMessage($data['message']);
                        $message->setExpireDate($data['expireDate']);
                        $message->save();
                    }
                    else
                    {
                        $message = new privateMessageModel();
                        $message->setMessage($data['message']);
                        $message->setExpireDate($data['expireDate']);
                        $message->setUserId($data['account']);
                        $message->save();
                    }

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
            $this->view->messages = $messages;
        }
        else
        {
            header("location: ".$this->_link(array("messages","account"))."");
        }
    }

    public function account()
    {
        $user = new userModel();
        if($user->getAuthLevel() >= 500)
        {
            $form = new newMessageForm();
            $form->setAction($this->_link(array("messages","account")));

            if($form->wasSubmitted())
            {
                $form->addData($_POST);
                if($form->validate())
                {
                    $data = $form->exportFormData();

                    if($data['account'] == 'all')
                    {
                        $message = new messageModel();
                        $message->setMessage($data['message']);
                        $message->setExpireDate($data['expireDate']);
                        $message->save();
                    }
                    else
                    {
                        $message = new privateMessageModel();
                        $message->setMessage($data['message']);
                        $message->setExpireDate($data['expireDate']);
                        $message->setUserId($data['account']);
                        $message->save();
                    }

                    $form = new newMessageForm();
                    $form->setAction($this->_link(array("messages","account")));
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
            $this->view->messages = $messages;
        }
        else
        {
            header("location: ".$this->_link(array("messages","account"))."");
        }
    }

    public function edit($id = null)
    {
        if($id != null)
        {
            $form = new editMessageForm();
            $message = new messageModel();

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

                    $message = new messageModel();
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

    public function editPrivate($id = null)
    {
        if($id != null)
        {
            $form = new editPrivateMessageForm();
            $message = new privateMessageModel();

            $data = $message->supervisorLoad($id);

            $form->setAction($this->_link(array('messages','edit',$message->getId())));
            $form->addData($data);

            if($form->wasSubmitted())
            {
                $form->addData($_POST);
                if($form->validate())
                {
                    $data = $form->exportFormData();

                    $message = new privateMessageModel();
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

    public function deleteprivate($id)
    {
        $message = new privateMessageModel();
        $message->deletePrivate($id);
        header("location:".$this->_link(array('messages'))."");
    }

    public function delete($id)
    {
        $message = new messageModel();
        $message->delete($id);
        header("location:".$this->_link(array('messages'))."");
    }

    public function expired()
    {
        $messages = new messagesModel();
        $this->view->messages = $messages->getExpiredMessages();
    }
}

?>