<?php
class Staple_Form_FoundationTimeElement extends Staple_Form_Element
{
	/**
	 * Size of the text field.
	 * @var int
	 */
	protected $size;
	/**
	 * Maxlength of the textfield.
	 * @var int
	 */
	protected $max;
	
	/**
	 * @return the $size
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return the $max
	 */
	public function getMax()
	{
		return $this->max;
	}

	/**
	 * @param int $size
	 */
	public function setSize($size)
	{
		$this->size = (int)$size;
		return $this;
	}

	/**
	 * @param int $max
	 */
	public function setMax($max)
	{
		$this->max = (int)$max;
		return $this;
	}

	/**
	 * Build the field label.
	 * @see Staple_Form_Element::label()
	 * @return string
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
	 * Build the field itself.
	 * @see Staple_Form_Element::field()
	 * @return string
	 */
	public function field()
	{
		$size = '';
		$max = '';
		if(isset($this->size))
		{
			$size = ' size="'.((int)$this->size).'"';
		}
		if(isset($this->max))
		{
			$max = ' maxlength="'.((int)$this->max).'"';
		}
		$classes = $this->getClassString();
		return '	<input type="time" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'"'.$classes.$size.$max.$this->getAttribString().'>'."\n";
	}

	/**
	 * Build the form field.
	 * @see Staple_Form_Element::build()
	 * @return string
	 */
	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/FoundationTimeElement.phtml';
		if(file_exists($view))
		{
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
		else
		{
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