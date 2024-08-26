<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class XHTML extends Control
{
   public $head = null;
   
   public $body = null;

   public function __construct($id = null)
   {
      parent::__construct($id);
      $this->attributes['xmlns'] = 'http://www.w3.org/1999/xhtml';
      $this->properties['doctype'] = 'transitional'; // may be also "strict", "frameset" nad "html".
   }
   
   public function render()
   {
      switch ($this->properties['doctype'])
      {
         default;
         case 'strict':
           $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
           break;
         case 'transitional':
           $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
           break;
         case 'frameset':
           $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
           break;
         case 'html':
           $html = '<!DOCTYPE html>';
           break;
      }  
      $body = $this->body->render();
      return $html . '<html' . $this->getParams() . '>' . $this->head->render() . $body . '</html>';
   }
}

?>
