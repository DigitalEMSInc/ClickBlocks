<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\Helpers;

class Body extends Panel
{
   public function __construct($id, $expire = 0)
   {
      parent::__construct($id, $expire);
      $this->properties['tag'] = 'body';
      $this->attributes['background'] = null;
      $this->attributes['bgcolor'] = null;
      $this->attributes['link'] = null;
      $this->attributes['alink'] = null;
      $this->attributes['vlink'] = null;
      $this->attributes['onload'] = null;
      $this->attributes['onunload'] = null;
      $this->attributes['onbeforeunload'] = null;
   }

   public function parse(array &$attributes, Core\ITemplate $tpl)
   {
      parent::parse($attributes, $tpl);
      if ($attributes['css'])
      {
         foreach (explode(',', $attributes['css']) as $css) $this->css->add(new Helpers\Style(null, null, Core\IO::url('css') . trim($css)), 'link');
         unset($attributes['css']);
      }
      if ($attributes['js'])
      {
         foreach (explode(',', $attributes['js']) as $js) $this->js->add(new Helpers\Script(null, null, Core\IO::url('js') . trim($js)), 'link');
         unset($attributes['js']);
      }
      return $this;
   }
 
   public function getInnerHTML()
   {
      $this->HTML()->CSS()->JS();
      $html = parent::getInnerHTML();
      return $this->html->render('top') . $html . $this->html->render('bottom') . $this->js->render('foot');     
   }
}

?>
