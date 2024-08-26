<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

abstract class Widget extends Panel
{
   private $template = null;

   public function __construct($id, $template = null)
   {
      parent::__construct($id);
      $this->properties['sortBy'] = 1;
      $this->properties['pageSize'] = 10;
      $this->properties['pos'] = 0;
      $this->properties['count'] = null;
      $this->properties['rows'] = null;
      $this->template = $template;
   }

   public function parse(array &$attributes, Core\ITemplate $tpl)
   {
      if ($this->template && !$attributes['template']) $attributes['template'] = $this->template;
      return parent::parse($attributes, $tpl);
   }

   public function init()
   {
      $this->sort($this->sortBy);
   }

   public function sort($sortBy)
   {
      $this->sortBy = -$sortBy;
   }

   public function unload()
   {
      $new = $this->getParameters();
      if ($this->properties['visible'] && (MVC\MVC::isFirstRequest() || $this->updated !== false || $this->page[$this->attributes['uniqueID']]['parameters'] != $new['parameters'])) $this->execute();
   }

   public function render()
   {
      if ($this->properties['visible'])
      {
         $this->adjustControls($this);
         $this->tpl->data = $this->getData();
         $this->tpl->uniqueID = $this->attributes['uniqueID'];
         $this->tpl->id = $this->properties['id'];
         $this->tpl->count = $this->properties['count'];
         $this->tpl->class = str_replace('\\', '\\\\', $this->properties['ctrlClass']);
         $this->tpl->pos = $this->properties['pos'];
      }
      return parent::render();
   }

   public function getXHTML()
   {
      $tag = strtolower(Utils\PHPParser::getClassName(get_class($this)));
      if ($this->template) return '<' . $tag . $this->getXHTMLParams() . ' template="' . htmlspecialchars($this->template) . '" />';
      $xml = '<' . $tag . $this->getXHTMLParams() . '>';
      $temp = (string)$this->tpl;
      foreach ($this as $uniqueID => $ctrl) $temp = str_replace('<?=$' . $uniqueID . ';?>', $ctrl->getXHTML(), $temp);
      $xml .= $temp;
      return $xml .= '</' . $tag . '>';
   }

   protected function adjustControls(IPanel $panel = null)
   {
      foreach ($panel as $ctrl)
      {
         switch (Utils\PHPParser::getClassName($ctrl))
         {
            case 'Navigator':
              $ctrl->callBack = str_replace('\\', '\\\\', $this->properties['ctrlClass']) . '@' . $this->attributes['uniqueID'] . '->__set';
              $ctrl->pos = $this->properties['pos'];
              $ctrl->pageSize = $this->properties['pageSize'];
              $ctrl->count = $this->properties['count'];
              break;
            case 'DropDownBox':
            case 'SQLDropDownBox':
              if ($ctrl->id == 'pageSize') $ctrl->value = $this->properties['pageSize'];
              break;
            case 'Panel':
              $this->adjustControls($ctrl);
              break;
         }
      }
      return $this;
   }

   protected function execute()
   {
      $this->properties['count'] = $this->getCount();
      $this->normalizeProperties();
      $this->properties['rows'] = $this->getRows();
      return $this;
   }

   protected function normalizeProperties()
   {
      $this->properties['pageSize'] = (int)$this->properties['pageSize'];
      $this->properties['pos'] = (int)$this->properties['pos'];
      if ($this->properties['pos'] < 0) $this->properties['pos'] = 0;
      if ($this->properties['pageSize'] < 1) $this->properties['pageSize'] = 0;
      $last = ceil($this->properties['count'] / $this->properties['pageSize']) - 1;
      if ($last < 0) $last = 0;
      if ($this->properties['pos'] > $last) $this->properties['pos'] = $last;
   }

   protected function getData(){}

   abstract public function getCount();
   abstract public function getRows();
}

?>
