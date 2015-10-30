<?php
class Staple_Form_FoundationButtonElement extends Staple_Form_Element
{
	public function __construct($name, $value=NULL, $label = NULL, $id = NULL, array $attrib = array())
	{
		parent::__construct($name,$label,$id,$attrib);
		if(isset($value))
		{
			$this->value = $value;
		}
	}
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		$classes = $this->getClassString();
		echo $classes;
		return '	<input type="button" class="button" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'"'.$this->getAttribString().">\n";
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return "	<label for=\"".$this->escape($this->id)."\"".$this->getClassString().">".$this->label."</label>\n";
	}

	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/FoundationButtonElement.phtml';
		if(file_exists($view))
		{
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
		else 
		{
			$buf .= "<div class=\"row\">\n";
			$buf .= "<div class=\"small-12 columns\">\n";
			$buf .= $this->field();
			$buf .= "</div>\n";
			$buf .= "</div>\n";
		}
		return $buf;
	}
}