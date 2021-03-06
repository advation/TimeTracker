<?php
/**
 * OO Forms. This class is used as a base for extending or a direct mechanism to create
 * object-based HTML forms.
 * 
 * @author Ironpilot
 * @copyright Copywrite (c) 2011, STAPLE CODE
 * 
 * This file is part of the STAPLE Framework.
 * 
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by the 
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 * 
 * The STAPLE Framework is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for 
 * more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

class Staple_Form
{
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const ENC_APP = 'application/x-www-form-urlencoded';
	const ENC_FILE = 'multipart/form-data';
	const ENC_TEXT = 'text/plain'; 
	/**
	 * The action (form submittal) location.
	 * @var string
	 */
	protected $action;
	
	/**
	 * Holds the form's method.
	 * @var string
	 */
	protected $method = "POST";
	/**
	 * The name of the form. This name identifies the form in both the session and on the 
	 * website's HTML. It needs to be unique to any other forms on the website.
	 * @var string
	 */
	protected $name;
	/**
	 * Stores the EncType of the form.
	 * @var string
	 */
	protected $enctype;
	/**
	 * Set the HTML target attribute.
	 * @var string
	 */
	protected $target;
	/**
	 * An array that holds a list of callback functions to be called upon form validation.
	 * @var array
	 */
	protected $callbacks = array();
	
	/**
	 * Contains an array of errors from form validation
	 * @var array
	 */
	protected $errors = array();
	
	/**
	 * An array of Staple_Form_Element objects, that represent the form fields.
	 * @var array[Staple_Form_Element]
	 */
	public $fields = array();
	
	/**
	 * A boolean value that signifys a valid submission of the form.
	 * @var boolean
	 */
	private $createIdent = true;
	
	/**
	 * A long identifying value for the form. This is used to identify a valid form submission.
	 * @var string
	 */
	protected $identifier;
	
	/**
	 * Boolean value that signifys a submittal of the form on the last HTTP request.
	 * @var boolean
	 */
	private $submitted = false;
	
	/**
	 * Holds a list of the HTML classes to apply to the form tag.
	 * @var array
	 */
	protected $classes = array();
	
	/**
	 * Holds a title for the form.
	 * @var string
	 */
	protected $title;
	
	/**
	 * Holds the form layout name.
	 * @var string
	 */
	protected $layout;
	/**
	 * Dynamic datastore.
	 * @var array
	 */
	protected $_store = array();
	
	/**
	 * @param string $name
	 * @param string $action
	 */
	final public function __construct($name = NULL, $action = NULL)
	{
		$this->_start();
		if(isset($name))
		{
			$this->name = $name;
		}
		if(isset($action))
		{
			$this->action = $action;
		}
		if(isset($this->name))
		{
			//check that the form was submitted.
			if(array_key_exists('Staple', $_SESSION))
			{
				if(array_key_exists('Forms', $_SESSION['Staple']))
				{
					if(array_key_exists($this->name, $_SESSION['Staple']['Forms']))
					{
						if(array_key_exists('ident', $_SESSION['Staple']['Forms'][$this->name]))
						{
							if(array_key_exists('ident', $_REQUEST))
							{
								if($_SESSION['Staple']['Forms'][$this->name]['ident'] == $_REQUEST['ident'])
								{
									$this->submitted = true;
								}
							}
						}
					}
				}
			}
		}
		
		//Repopulate data from the session -- I might add this.....
		//if($this->wasSubmitted())
		//{
			
		//}
		
		//create the form's identity field.
		if($this->createIdent === true)
		{
			$this->createIdentifier();
		}
	}
	
	public function __destruct()
	{
		if(isset($this->name) && $this->createIdent === TRUE)
		{
			$_SESSION['Staple']['Forms'][$this->name]['ident'] = $this->identifier;
		}
	}
	
	/**
	 * Overloaded __set allows for dynamic addition of properties.
	 * @param string | int $key
	 * @param mixed $value
	 */
	public function __set($key,$value)
	{
		$this->_store[$key] = $value;
	}
	
	/**
	 * Retrieves a stored property.
	 * @param string | int $key
	 */
	public function __get($key)
	{
		if(array_key_exists($key,$this->_store))
		{
			return $this->_store[$key];
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * Boot function for initialization of forms that extend this class.
	 */
	public function _start()
	{
		
	}
	
	/**
	 * Creates the ident field, adds it to the form and save the value in the session. This is used
	 * to verify the form has been submitted and also aids in preventing CSRF form attacks.
	 */
	protected function createIdentifier()
	{
		$this->identifier = Staple_Encrypt::genHex(32);
		$ident = new Staple_Form_HiddenElement('ident');
		$ident->setValue($this->identifier)
			->setReadOnly();
		$this->addField($ident);
	}
	public function disableIdentifier()
	{
		unset($this->identifier);
		$this->createIdent = false;
		unset($this->fields['ident']);
		return $this;
	}
	
	/**
	 * The toString magic method calls the forms build function to output the form to the website.
	 */
	public function __toString()
	{
		try {
			return $this->build();
		}
		catch (Exception $e)
		{
			$msg = '<p class=\"formerror\">Form Error....</p>';
			if(Staple_Config::getValue('errors', 'devmode'))
			{
				$msg .= '<p>'.$e->getMessage().'</p>';
			}
			return $msg;
		}
	}
	
	/**
	 * A factory function to encapsulate the creation of form objects.
	 * @param string $name
	 * @param string $action
	 * @param string $method
	 * @return Staple_Form
	 */
	public static function Create($name, $action=NULL, $method="POST")
	{
		$inst = new self($name,$action);
		$inst->setMethod($method);
		return $inst;
	}
	
	/**
	 * Adds a field to the form from an already instantiated form element.
	 * @param Staple_Form_Element $field
	 */
	public function addField(Staple_Form_Element $field)
	{
		$args = func_get_args();
		foreach($args as $newField)
		{
			if($newField instanceof Staple_Form_FileElement)
			{
				$this->setEnctype(self::ENC_FILE);
			}
			if($newField instanceof Staple_Form_Element)
			{
				$this->fields[$newField->getName()] = $newField;
			}
		}
		return $this;
	}
	
	/**
	 * Accepts an associative array of fields=>values to apply to the form elements.
	 * @param array $data
	 */
	public function addData(array $data)
	{
		foreach($this->fields as $fieldname=>$obj)
		{
			if(array_key_exists($fieldname, $data))
			{
				$obj->setValue($data[$fieldname]);
			}
			elseif($obj instanceof Staple_Form_CheckboxGroup)
			{
				$boxes = $obj->getBoxes();
				foreach($boxes as $chk)
				{
					if(array_key_exists($chk->getName(), $data))
					{
						$chk->setValue($data[$chk->getName()]);
					}
				}
			}
			else
			{
				//Checkbox Fix
				if($obj->isDisabled() === false && $obj instanceof Staple_Form_CheckboxElement)
				{
					$obj->setValue(NULL);
				}
			}
		}
		return $this;
	}
	
	/**
	 * Returns an associative array of the field values with the field names as the keys, including
	 * the identity field.
	 * @return array
	 */
	public function exportFormData()
	{
		$data = array();
		foreach($this->fields as $name=>$field)
		{
			$data[$field->getName()] = $field->getValue();
		}
		return $data;
	}
	
	/**
	 * Returns the value of $this->submitted
	 * @return bool
	 */
	public function wasSubmitted()
	{
		if(isset($this->name) && isset($_SESSION['Staple']['Forms'][$this->name]['ident']) && isset($_REQUEST['ident']))
		{
			if($_SESSION['Staple']['Forms'][$this->name]['ident'] == $_REQUEST['ident'])
			{
				$this->submitted = true;
				return true;
			}
		}
		$this->submitted = false;
		return false;
		//return $this->submitted;
	}
	
	/**
	 * Adds an HTML class to the form.
	 * @param string $class
	 */
	public function addClass($class)
	{
		if(!in_array($class,$this->classes))
		{
			$this->classes[] = $class;
		}
		return $this;
	}
	
	/**
	 * Removes an HTML class from the form.
	 * @param string $class
	 */
	public function removeClass($class)
	{
		if(($key = array_search($class,$this->classes)) !== false)
		{
			unset($this->classes[$key]);
		}
		return $this;
	}
	
	/**
	 * Checks that the specified field exists on the form and that it is instantiated.
	 * @param string $field
	 * @return boolean
	 */
	public function fieldExists($field)
	{
		if(array_key_exists($field, $this->fields))
		{
			if($this->fields[$field] instanceof Staple_Form_Element)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @todo complete the javascript validators.....
	 * 
	 */
	public function clientJS()
	{
		
	}
	
	public function clientJQuery()
	{
		$script = <<<JS
var {$this->name}validated = false;
$(function (){
	$('#{$this->name}_form').submit(function (){
	var errors = new Array();
JS;
		
		foreach($this->fields as $field)
		{
			if($field instanceof Staple_Form_Element)
			{
				if($field->isRequired())
				{
					$script .= $field->clientJQuery();
				}
			}
			else
			{
				throw new Exception('Form Error', Staple_Error::FORM_ERROR);
			}
		}
		
		
		$script .= <<<JS
	if(errors.length > 0)
	{
		var msg  = 'Please correct these form errors:\\n';
		var count = 0;
		for(var x in errors)
		{
			count++;
			if(count <= 10)
			{
				msg += '\\n'+errors[x];
			}
		}
		if(count > 10)
		{
			msg += '\\nAnd '+count+' more...';
		}
		alert(msg);
		//jQuery UI Dialog
		//$('<div class="form_validation_dialog" title="Form Errors">'+msg+'</div>').dialog({modal:true, buttons: {'Ok': function(){ $(this).dialog('close'); }}});
		{$this->name}validated = false;
		return false;
	}
	else
	{
		{$this->name}validated = true;
	}
	})
});
JS;
		$script .= "";
		$script .= "";
		
		//$script = "$('#{$this->name}_form').submit(false);\n";
		return $script;
	}
	
	/**
	 * Runs the validators on each field and checks for the completion of required fields.
	 * 
	 * @throws Exception
	 */
	public function validate()
	{
		$this->clearErrors();
		
		//Process validation callbacks.
		foreach($this->callbacks as $func)
		{
			try{
				$result = call_user_func_array($func['func'],$func['params']);
			}
			catch (Exception $e)
			{
				$result = false;
			}

            /*
			if(is_array($result))
			{
				$this->errors[] = array('label'=>'Additional Form Validation','errors'=>array($result));
			}
			else
			{
				$result = (bool)$result;
				if($result === false)
				{
					$this->errors[] = array('label'=>'Additional Form Validation','errors'=>array(array('Form Validation Returned False.')));
				}
			}
            */
		}
		
		//Process all validation fields.
		foreach($this->fields as $field)
		{
			if($field instanceof Staple_Form_Element)
			{
				if(!$field->isValid())
				{
					if($field->isRequired())
					{
						$this->errors[$field->getName()] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
					}
					elseif($field->getValue() != '')
					{
						//A few extra steps to handle File Uploads
						if($field instanceof Staple_Form_FileElement)
						{
							if(is_array($field->getValue()))
							{
								$file = $field->getValue();
								if(isset($file['error']))
								{
									if($file['error'] != UPLOAD_ERR_NO_FILE)
									{
										$this->errors[$field->getName()] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
									}
								}
								else
								{
									$this->errors[$field->getName()] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
								}
							}
							else
							{
								$this->errors[$field->getName()] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
							}
						}
						else
						{
							$this->errors[$field->getName()] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
						}
					}
				}
			}
			else
			{
				throw new Exception('Form Error', Staple_Error::FORM_ERROR);
			}
		}
		
		//Check for errors.
		if(count($this->errors) > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Add a single error to the form errors
	 * @param string $label
	 * @param string $msg
	 */
	public function addError($label,$msg)
	{
		$this->errors[] = array('label'=>$label,'errors'=>array(array($msg)));
	}
	
	/**
	 * @return the $errors
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Clear out the form errors before validating.
	 */
	protected function clearErrors()
	{
		$this->errors = array();
		return $this;
	}
	
	/**
	 * Adds a callback function to the validation stack. The function can be any standard callback, including an annonymous function.
	 * A callback function must return a boolean true on success, and boolean false or an array of errors on failure.
	 * 
	 * @param callback $func
	 * @param array $params
	 */
	public function addValidationCallback($func,$params = array())
	{
		array_push($this->callbacks, array('func'=>$func,'params'=>$params));
		return $this;
	}
	
	/*----------------------------------------Getters and Setters----------------------------------------*/
	
	/**
	 * Sets the form action location
	 * @param string $action
	 * @return Staple_Form
	 */
	public function setAction($action)
	{
		$this->action = $action;
		return $this;
	}
	
	/**
	 * Returns the action location of the form.
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}
	
	/**
	 * Sets the method for the form. Only accepts GET and POST. POST is the default.
	 * @param string $method
	 * @return Staple_Form
	 */
	public function setMethod($method)
	{
		if(strtoupper($method) == "GET")
		{
			$this->method = "GET";
		}
		else
		{
			$this->method = "POST";
		}
		return $this;
	}
	
	/**
	 * Returns the method, either GET or POST
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}
	
	/**
	 * Sets the name of the form
	 * @param string $name
	 * @return Staple_Form
	 */
	public function setName($name)
	{
		$this->name = str_replace(' ','_',$name);
		return $this;
	}
	
	/**
	 * Returns the name of the form.
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @return the $enctype
	 */
	public function getEnctype()
	{
		return $this->enctype;
	}

	/**
	 * @param string $enctype
	 */
	public function setEnctype($enctype)
	{
		switch($enctype)
		{
			case self::ENC_APP:
			case self::ENC_FILE:
				case self::ENC_TEXT:
				$this->enctype = $enctype;
				break;
		}
		return $this;
	}

	/**
	 * @return the $title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return the $layout
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * @param string $layout
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;
		return $this;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	/**
	 * @return the $target
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * @param string $target
	 */
	public function setTarget($target)
	{
		$this->target = $target;
		return $this;
	}

	public function getFieldValue($fieldname)
	{
		if(array_key_exists($fieldname,$this->fields))
		{
			if($this->fields[$fieldname] instanceof Staple_Form_Element)
			{
				return $this->fields[$fieldname]->getValue();
			}
		}
		return NULL;
	}
	
	/*----------------------------------------Builders----------------------------------------*/
	
	public function formstart()
	{
		$buf = '';
		$buf .= "\n<form name=\"{$this->name}\" id=\"{$this->name}_form\" action=\"{$this->action}\" method=\"{$this->method}\"";
		if(isset($this->enctype))
		{
			$buf .= ' enctype="'.$this->enctype.'"';
		}
		if(isset($this->target))
		{
			$buf .= ' target="'.$this->target.'"';
		}
		if(count($this->classes) > 0)
		{
			$buf .= ' class="';
			$cstring = '';
			foreach($this->classes as $class)
			{
				$cstring .= $class.' ';
			}
			$buf .= trim($cstring);
			$buf .= '"';
		}
		$buf .= ">\n";
		$buf .= "<div id=\"{$this->name}_div\"";
		if(count($this->classes) > 0)
		{
			$buf .= ' class="'.trim($cstring).'"';
		}
		$buf .= ">\n";
		return $buf;
	}
	public function formend()
	{
		$buf = "\n";
		if(array_key_exists('ident', $this->fields))
		{
			if($this->fields['ident'] instanceof Staple_Form_Element)
			{
				$buf .= $this->fields['ident']->build();
			}
		}
		$buf .= "\n</div>\n</form>\n";
		return $buf;
	}
	
	public function title()
	{
		return $this->title;
	}
	
	public function fields()
	{
		$buf = '';
		foreach($this->fields as $field)
		{
			if($field->getName() != 'ident')
			{
				$buf .= $field->build();
			}
		}
		return $buf;
	}
	
	/**
	 * Constructs and echos the HTML for the form and all of its elements.
	 */
	public function build()
	{
		$buf = '';
		if(isset($this->layout))
		{
			$layoutloc = FORMS_ROOT.'layouts/'.basename($this->layout).'.phtml';
			if(file_exists($layoutloc))
			{
				ob_start();
				include $layoutloc;
				$buf = ob_get_contents();
				ob_end_clean();
			}
			else 
			{
				throw new Exception('Unable to load form layout.', Staple_Error::FORM_ERROR);
			}
		}
		else
		{
			$buf .= $this->formstart();
			$buf .= $this->title();
			$buf .= $this->fields();
			$buf .= $this->formend();
		}
		return $buf;
	}
	
	/*----------------------------------------Helpers----------------------------------------*/
	/**
	 * 
	 * If an array is supplied, a link is created to a controller/action. If a string is
	 * supplied, a file link is specified.
	 * @param string | array $link
	 * @param array $get
	 */
	public function link($link,array $get = array())
	{
		return Staple_Link::get($link,$get);
	}
	/**
	 * @see Staple_View::escape()
	 * @param string $estring
	 * @param boolean $strip
	 */
	public static function escape($estring, $strip = false)
	{
		return Staple_View::escape($estring,$strip);
	}
}