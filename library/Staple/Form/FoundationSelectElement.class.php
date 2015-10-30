<?php
class Staple_Form_FoundationSelectElement extends Staple_Form_Element
{
	const SORT_VALUES = 1;
	const SORT_LABELS_ALPHA = 2;
	const SORT_LABELS_REVERSE = 3;
	
	/**
	 * An array that holds the options list for the select box. The keys represent the values of the options,
	 * and the values of the array are the labels for the options.
	 * @var array
	 */
	protected $options = array();
	/**
	 * Boolean to signify a selected element.
	 * @var bool
	 */
	private $selected = false;
	/**
	 * Defines the size of the select element
	 * @var int
	 */
	protected $size = 1;
	/**
	 * Boolean to signify that multiple options are selectable in the select element.
	 * @var bool
	 */
	protected $multiple = false;
	
	/**
	 * Returns a boolean whether the checkbox is checked or not.
	 * @return boolean
	 */
	public function isSelected()
	{
		if($this->selected === true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Returns a boolean whether the box is a multi-select or not
	 * @return bool
	 */
	public function isMultiple()
	{
		return $this->multiple;
	}

    /**
     * Set the multiple attribute of the select element.
     * @param bool $bool
     * @return $this
     */
	public function setMultiple($bool = true)
	{
		$this->multiple = (bool)$bool;
		return $this;
	}
	
	/**
	 * Sets the value for the select box
	 * @param boolean $val
	 * @return Staple_Form_CheckboxElement
	 */
	public function setValue($val)
	{
		$this->selected = true;
		return parent::setValue($val);
	}

    /**
     * Sets the size of the select element
     * @param int $size
     * @return $this
     */
	public function setSize($size)
	{
		$this->size = (int)$size;
		return $this;
	}
	
	/**
	 * Returns the size of the select element
	 * @return int
	 */
	public function getSize()
	{
		return $this->size;
	}

    /**
     * Add a single option to the select list.
     *
     * @param mixed $value
     * @param string $label
     * @return $this
     * @throws Exception
     */
	public function addOption($value,$label = NULL)
	{
		if(is_array($value) || is_resource($value))
		{
			throw new Exception('Select values must be strings or integers.', Staple_Error::APPLICATION_ERROR);
		}
		else 
		{
			if(isset($label))
			{
				$this->options[$value] = $label;
			}
			else
			{
				$this->options[$value] = $value;
			}
		}
		return $this;
	}

    /**
     * Add an array of values to the select list. Keys of the array become values of the options and the values
     * become the labels for the options. The second option allows the use of the labels as the values for the
     * options.
     *
     * @param array $options
     * @param boolean $labelvalues
     * @return $this
     * @throws Exception
     */
	public function addOptionsArray(array $options, $labelvalues = FALSE)
	{
		foreach($options as $value=>$label)
		{
			if(is_array($value) || is_resource($value))
			{
				throw new Exception('Select values must be strings or integers.', Staple_Error::APPLICATION_ERROR);
			}
			else
			{
				if($labelvalues === true)
				{
					$this->options[$label] = $label;
				}
				else 
				{
					$this->options[$value] = $label;
				}
			} 
		}
		return $this;
	}
	
	/**
	 * Removes all the options from the select list.
	 */
	public function clearOptionList()
	{
		$this->options = array();
		return $this; 
	}
	
	/**
	 * Returns the options array.
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

    /**
     * Sorts the options list based on a set of preset sorts.
     * @param int $how
     * @return $this
     */
	public function sortOptions($how)
	{
		switch($how)
		{
			case self::SORT_VALUES :
				ksort($this->options);
				break;
			case self::SORT_LABELS_ALPHA :
				asort($this->options);
				break;
			case self::SORT_LABELS_REVERSE :
				arsort($this->options);
				break;
		}
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		$buf = '';
		$buf .= "	<select name=\"".$this->escape($this->name)."\" id=\"".$this->escape($this->id)."\"";
		if($this->size > 1)
		{
			$buf .= ' size="'.(int)$this->getSize().'"';
		}
		if($this->multiple === true)
		{
			$buf .= ' multiple="multiple"';
		}
		$buf .= $this->getAttribString().">\n";
		foreach($this->options as $value=>$label)
		{
			$select = '';
			if($this->value == $value && $this->isSelected())
			{
				$select = ' selected';
			}
			$buf .= "		<option value=\"".$this->escape($value)."\"$select>".$this->escape($label)."</option>\n";
		}
		$buf .= "	</select>\n";
		return $buf;
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		if(count($this->errors) != 0)
		{
			$buf = "<label for=\"".$this->escape($this->id)."\" class=\"error\">";	
		}
		else
		{
			$buf = "<label for=\"".$this->escape($this->id)."\">";
		}
		
		if($this->required == 1)
		{
			$buf .= "<b>";
			$buf .= $this->label;
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $this->label;
		}

		$buf .= "</label>\n";
		return $buf;
	}

	/**
	 * Builds the select list form element.
	 * 
	 * @see Staple_Form_Element::build()
	 */
	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/FoundationSelectElement.phtml';
		if(file_exists($view))
		{
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$classes = $this->getClassString();
			$buf .= "<div class=\"row\">\n"; //Row Start
			$buf .= "<div class=\"small-12 columns\">\n"; //Label Start
			$buf .= $this->label();
			$buf .= "</div>\n"; //Label End
			$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
			if(count($this->errors) != 0)
			{
				$buf .= "<label class=\"error\">";
			}
			
			$buf .= $this->field();
			
			if(count($this->errors) != 0)
			{
				$buf .= "</label>";
				$buf .= "<small class=\"error\">";
				foreach($this->errors as $error)
				{
					foreach($error as $message)
					{
						$buf .= "- $message<br>\n";
					}
				}
				$buf .= "</small>";
			}
			$buf .= "</div>\n"; //Field End
			$buf .= "</div>\n"; //Row end
		}
		return $buf;
	}
}