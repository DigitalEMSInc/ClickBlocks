<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class Head extends Control
{
   protected $meta = array();
   protected $links = array();
   protected $js = null;
   protected $css = null;

   public function __construct($name = null)
   {
      parent::__construct();
      $this->js = Web\JS::getInstance();
      $this->css = Web\CSS::getInstance();
      $this->attributes['profile'] = null;
      $this->properties['name'] = $name;
      $this->properties['icon'] = null;
   }

    public function addLink(HeadLink $obj)
    {
        $this->links[$obj->id] = $obj;
        return $this;
    }

   public function addMeta(Meta $obj)
   {
      $this->meta[$obj->id] = $obj;
      return $this;
   }

   public function setMeta($id, Meta $obj)
   {
      $this->meta[$id] = $obj;
      return $this;
   }

   public function getMeta($id)
   {
      return $this->meta[$id];
   }

   public function deleteMeta($id)
   {
      unset($this->meta[$id]);
      return $this;
   }

   public function render()
   {
      if ($this->properties['icon'] != '')
      {
         $icon = new Style('headicon');
         $icon->rel = 'shortcut icon';
         $icon->type = 'image/x-icon';
         $icon->href = $this->icon;
         $this->css->add($icon, 'link');
      }
      else $this->css->delete('headicon', 'link');
      $dom = $this->js->render('domready');
      $html = '<head' . $this->getParams() . '>' . PHP_EOL;
      foreach ($this->meta as $obj) $html .= $obj->render() . PHP_EOL;
      foreach ($this->links as $obj) $html .= $obj->render() . PHP_EOL;
      $html .= '<title>' . htmlspecialchars($this->properties['name']) . '</title>';
      $html .= $this->css->render('link');
      $html .= $this->js->render('link');
      $html .= $this->css->render('style');
      $html .= $this->js->render('head');
      $html .= $dom;
      $html .= '</head>';
      return $html;
   }
}

?>
