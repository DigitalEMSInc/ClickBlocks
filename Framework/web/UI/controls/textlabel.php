<?php

namespace ClickBlocks\Web\UI\POM;

class TextLabel extends WebControl
{
   public function __construct($id, $text = null, $for = null)
   {
      parent::__construct($id);
      $this->attributes['for'] = $for;
      $this->properties['text'] = $text;
      $this->properties['type'] = null;
      $this->properties['tag'] = 'label';
   }
   
   public function render()
   {
	  $for = $this->attributes['for'];
	  if ($this->attributes['for'] != '')
	  {
		 $ctrl = $this->page->get($this->attributes['for']);
		 if ($ctrl !== false) $this->attributes['for'] = $ctrl->uniqueID;
	  }
      if (!$this->properties['visible']) return $this->invisible();
      $html = '<' . $this->properties['tag'] . $this->getParams() . '>' . $this->properties['text'] . '</' . $this->properties['tag'] . '>';
      $this->attributes['for'] = $for;
      return $html;
   }
}

?>
