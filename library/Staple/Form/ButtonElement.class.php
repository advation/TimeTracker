<?php
/**
 * Submit button element for use on forms.
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

class Staple_Form_ButtonElement extends Staple_Form_Element
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
		return '	<input type="button" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'"'.$this->getAttribString().">\n";
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
		$view = FORMS_ROOT.'/fields/SubmitElement.phtml';
		if(file_exists($view))
		{
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
		else 
		{
			$buf .= $this->field();
		}
		return $buf;
	}
}