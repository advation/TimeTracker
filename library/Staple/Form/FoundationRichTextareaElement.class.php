<?php
class Staple_Form_FoundationRichTextareaElement extends Staple_Form_Element
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
		$classes = $this->getClassString();
		return '	<textarea style="display:none;" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" '.$this->getAttribString().' '.$classes.' >'.$this->escape($this->value).'</textarea>'."\n";
	}

	/**
	 * Build the form field.
	 * @see Staple_Form_Element::build()
	 * @return string
	 */
	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/FoundationRichTextareaElement.phtml';
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
			$buf .= "
				<ul class=\"button-group\" style=\"padding:0; margin:0;\">
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Bold\">
							<a href=\"#\" id=\"iBold\" class=\"button secondary small\"><i class=\"fi-bold\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Italic\">
							<a href=\"#\" id=\"iItalic\" class=\"button secondary small\"><i class=\"fi-italic\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Underline\">
							<a href=\"#\" id=\"iUnderline\" class=\"button secondary small\"><i class=\"fi-underline\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Font Color\">
							<a href=\"#\" id=\"iColor\" class=\"button secondary small\"><i class=\"fi-text-color\" style=\"color:red;\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Font Size\">
							<a href=\"#\" id=\"iSize\" class=\"button secondary small\">+/-</a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Ordered List\">
							<a href=\"#\" id=\"iOList\" class=\"button secondary small\"><i class=\"fi-list-thumbnails\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Unordered List\">
							<a href=\"#\" id=\"iUList\" class=\"button secondary small\"><i class=\"fi-list\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"More Indent\">
							<a href=\"#\" id=\"iIndent\" class=\"button secondary small\"><i class=\"fi-indent-more\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Less Indent\">
							<a href=\"#\" id=\"iOutdent\" class=\"button secondary small\"><i class=\"fi-indent-less\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Justify Center\">
							<a href=\"#\" id=\"iJCenter\" class=\"button secondary small\"><i class=\"fi-align-center\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Justify Left\">
							<a href=\"#\" id=\"iJLeft\" class=\"button secondary small\"><i class=\"fi-align-left\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Justify Right\">
							<a href=\"#\" id=\"iJRight\" class=\"button secondary small\"><i class=\"fi-align-right\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Insert Image\">
							<a href=\"#\" id=\"iImage\" class=\"button secondary small\"><i class=\"fi-photo\"></i></a>
						</span>
					</li>
					<li>
						<span data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"Clear Formating\">
							<a href=\"#\" id=\"clearFormatting\" class=\"button secondary small\"><i class=\"fi-x\"></i></a>
						</span>
					</li>
				</ul>
			";
			$buf .= "<textarea name=\"i".$this->getId()."\" id=\"i".$this->getId()."\" ".$classes." style=\"width:100%; border:1px #ccc solid; min-height:250px;\"></textarea>";
			$buf .= "<script>
					$(document).ready(function() 
					{	
						i".$this->getId().".document.designMode = 'On';

						$('#iBold').click(function() {
							i".$this->getId().".document.execCommand('bold',false,null);
							return false;
						});

						$('#iItalic').click(function() {
							i".$this->getId().".document.execCommand('italic',false,null);
							return false;
						});

						$('#iUnderline').click(function() {
							i".$this->getId().".document.execCommand('underline',false,null);
							return false;
						});

						$('#iColor').click(function() {
							var color = prompt('Define a basic color or apply a hexadecimal color:','');
							i".$this->getId().".document.execCommand('ForeColor',false,color);
							return false;
						});

						$('#iOList').click(function() {
							i".$this->getId().".document.execCommand('InsertOrderedList',false,'newOL');
							return false;
						});

						$('#iUList').click(function() {
							i".$this->getId().".document.execCommand('InsertUnorderedList',false,'newUL');
							return false;
						});

						$('#iIndent').click(function() {
							i".$this->getId().".document.execCommand('indent',false,'');
							return false;
						});

						$('#iOutdent').click(function() {
							i".$this->getId().".document.execCommand('outdent',false,'');
							return false;
						});

						$('#iSize').click(function() {
							var size = prompt('Define a font size from 1 to 7:','');
							i".$this->getId().".document.execCommand('FontSize',false,size);
							return false;
						});

						$('#iLink').click(function() {
							var url = prompt('Enter the URL:','http://');
							i".$this->getId().".document.execCommand('CreateLink',false,url);
							return false;
						});

						$('#iJCenter').click(function() {
							i".$this->getId().".document.execCommand('justifyCenter',false,'');
							return false;
						});

						$('#iJRight').click(function() {
							i".$this->getId().".document.execCommand('justifyRight',false,'');
							return false;
						});

						$('#iJLeft').click(function() {
							i".$this->getId().".document.execCommand('justifyLeft',false,'');
							return false;
						});

						$('#iImage').click(function() {
							var url = prompt('Enter the URL from the image:','http://');
							i".$this->getId().".document.execCommand('insertImage',false,url);
							return false;
						});

						$('#clearFormatting').click(function() {
							i".$this->getId().".document.execCommand('removeFormat',false,'');
							return false;
						});

						$('#submit').click(function() {

						});
						

					});
					</script>
			";
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