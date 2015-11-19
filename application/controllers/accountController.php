<?php
class accountController extends Staple_AuthController
{
	protected $Account;
	
	public function _start()
	{
		$this->_setLayout("account");
		$this->_openMethod('admin');
	}
	
	public function index()
	{
		$form = new accountForm();
		if($form->wasSubmitted())
		{
			$form->addData($_POST);
			if($form->validate())
			{
				$pin = $_POST['pin'];
				$auth = Staple_Auth::get();

				$granted = $auth->doAuth(array('pin'=>$pin));

				if($granted === true)
				{
					header('Location: '.$this->_link(array('index','index')));
				}
				else
				{
					$this->view->message = "Invalid PIN";
					$this->view->form = $form;
					$this->layout->addScriptBlock('
							$(document).ready(function()
							{
								$(\'#errorMessage\').foundation(\'reveal\',\'open\');
							});
						');
				}
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

	public function account()
	{
		echo Staple_Auth::get()->getAuthLevel();
	}

	public function admin()
	{
		$form = new adminAccountForm();
		if($form->wasSubmitted())
		{
			$form->addData($_POST);
			if($form->validate())
			{
				$password = $_POST['password'];
				$account = $_POST['username'];

				$auth = Staple_Auth::get();

				$granted = $auth->doAuth(array('username'=>$account,'password'=>$password));

				if($granted === true)
				{
					header('Location: '.$this->_link(array('timesheet','index')));
				}
				else
				{
					$this->view->message = "Invalid login";
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
			$this->view->form = $form;
		}

	}

	public function logout()
	{
		$auth = Staple_Auth::get();
		$auth->clearAuth();
		header('Location: '.$this->_link(array('account','index')));
		exit(0);
	}	
}
?>