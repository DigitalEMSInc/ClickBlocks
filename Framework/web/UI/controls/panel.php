<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\Web;

class Panel extends WebControl implements IPanel
{
   protected $controls = array();

   public function __construct($id)
   {
      parent::__construct($id);
      $this->properties['expire'] = 0;
      $this->properties['tag'] = 'div';
      $this->properties['cacheID'] = null;
   }

   public function getIterator()
   {
      $ctrls = array();
      foreach ($this->controls as $uniqueID => $flag) if ($flag) $ctrls[] = $uniqueID;
      return new POMIterator($ctrls);
   }

   public function parse(array &$attributes, Core\ITemplate $tpl)
   {
      if ($attributes['template'])
      {
         $this->setTemplate(Web\XHTMLParser::exe(Web\XHTMLParser::decodePHPTags($attributes['template'])), $tpl);
         unset($attributes['template']);
      }
      else
      {
         if (!$this->properties['parentUniqueID']) $tpl->setTemplate($this->attributes['uniqueID'], '');
         else $tpl->setTemplate($this->attributes['uniqueID'], '', $this->properties['parentUniqueID'], $this->attributes['uniqueID']);
      }
      return parent::parse($attributes, $tpl);
   }

   public function setTemplate($template, Core\ITemplate $tpl = null)
   {
      $tpl = ($tpl) ?: $this->page->tpl;
      $template = '<panel id="' . uniqid('c') . '">' . ((is_file(Core\IO::dir($template))) ? file_get_contents(Core\IO::dir($template)) : $template) . '</panel>';
      $parser = new Web\XHTMLParser();
      $obj = $parser->parse($template, $template, false);
      $tpl->setTemplate($this->attributes['uniqueID'], '');
      foreach ($obj as $child)
      {
         $params = $child->getParameters();
         $params['parameters'][1]['parentUniqueID'] = '';
         $child->setParameters($params);
         $this->add($child);
         $this->copyTemplates($this, $child, $template, $tpl);
      }
      $template = (string)$template[$obj->uniqueID];
      if (!$this->properties['parentUniqueID']) $tpl->setTemplate($this->attributes['uniqueID'], $template);
      else $tpl->setTemplate($this->attributes['uniqueID'], $template, $this->properties['parentUniqueID'], $this->attributes['uniqueID']);
      unset($this->page[$obj->uniqueID]);
      $parser->adjustValidators();
      return $this;
   }

   public function count()
   {
      return count($this->controls);
   }

   public function offsetSet($uniqueID, $ctrl)
   {
      $this->replaceByUniqueID($uniqueID, $ctrl);
   }

   public function offsetExists($uniqueID)
   {
      return isset($this->controls[$uniqueID]);
   }

   public function offsetUnset($uniqueID)
   {
      $this->deleteByUniqueID($uniqueID);
   }

   public function offsetGet($uniqueID)
   {
      return $this->getByUniqueID($uniqueID);
   }

   public function getControls()
   {
      return $this->controls;
   }

   public function __set($param, $value)
   {
      if ($param == 'tpl') $this->page->tpl[$this->attributes['uniqueID']] = $value;
      else parent::__set($param, $value);
   }

   public function __get($param)
   {
      if ($param == 'tpl') return $this->page->tpl[$this->attributes['uniqueID']];
      return parent::__get($param);
   }

   public function __isset($param)
   {
      if ($param == 'tpl') return isset($this->page->tpl[$this->attributes['uniqueID']]);
      return parent::__isset($param);
   }

   public function isExpired()
   {
      return $this->reg->cache->isExpired($this->properties['cacheID'], $this->properties['expire']);
   }

   public function add(IWebControl $ctrl)
   {
      foreach ($this->controls as $uniqueID => $v)
      {
         $vs = $this->page->getActualVS($uniqueID);
         if ($vs['parameters'][1]['id'] == $ctrl->id)
         throw new \Exception(err_msg('ERR_CTRL_5', array(get_class($ctrl), $ctrl->getFullID(), $this->getFullID())));
      }
      $ctrl->parentUniqueID = $this->attributes['uniqueID'];
      $this->controls[$ctrl->uniqueID] = true;
      $this->page[$ctrl->uniqueID] = $ctrl;
      return $this;
   }

   public function insert(IWebControl $ctrl, $id = null)
   {
      if ($id === null || $id == $this->attributes['uniqueID'])
      {
         $this->tpl = '<?=$' . $ctrl->uniqueID . ';?>';
         $id = $this->getRepaintID();
      }
      else if (isset($this->controls[$id]))
      {
         $panel = $this[$id];
         if ($panel instanceof IPanel) return $panel->insert($ctrl);
         throw new \Exception(err_msg('ERR_CTRL_11', array($panel->getFullID())));
      }
      else
      {
         $dom = new Core\DOMDocumentEx();
         $dom->loadHTML($this->tpl);
         $dom->insert($id, '<?=$' . $ctrl->uniqueID . ';?>');
         $this->tpl = $dom->getHTML();
      }
      $this->add($ctrl);
      $this->ajax->insert($ctrl->render(), $id, 0, true);
      return $this;
   }

   public function inject(IWebControl $ctrl, $id = null, $mode = Core\DOMDocumentEx::DOM_INJECT_TOP)
   {
      if ($id == null || $id == $this->attributes['uniqueID'])
      {
         switch ($mode)
         {
            case Core\DOMDocumentEx::DOM_INJECT_TOP:
              $this->tpl = '<?=$' . $ctrl->uniqueID . ';?>' . $this->tpl;
              break;
            case Core\DOMDocumentEx::DOM_INJECT_BOTTOM:
              $this->tpl .= '<?=$' . $ctrl->uniqueID . ';?>';
              break;
            case Core\DOMDocumentEx::DOM_INJECT_AFTER:
            case Core\DOMDocumentEx::DOM_INJECT_BEFORE:
              throw new \Exception(err_msg('ERR_CTRL_12', array($this->getFullID())));
         }
         $id = $this->attributes['uniqueID'];
      }
      else if (isset($this->controls[$id]))
      {
         switch ($mode)
         {
            case Core\DOMDocumentEx::DOM_INJECT_TOP:
            case Core\DOMDocumentEx::DOM_INJECT_BOTTOM:
              $panel = $this[$id];
              if ($panel instanceof IPanel) return $panel->inject($ctrl, $id, $mode);
              throw new \Exception(err_msg('ERR_CTRL_11', array($panel->getFullID())));
            case Core\DOMDocumentEx::DOM_INJECT_AFTER:
              $this->tpl = str_replace('<?=$' . $id . ';?>', '<?=$' . $id . ';?><?=$' . $ctrl->uniqueID . ';?>', $this->tpl);
              break;
            case Core\DOMDocumentEx::DOM_INJECT_BEFORE:
              $this->tpl = str_replace('<?=$' . $id . ';?>', '<?=$' . $ctrl->uniqueID . ';?><?=$' . $id . ';?>', $this->tpl);
              break;
         }
      }
      else
      {
         $dom = new Core\DOMDocumentEx();
         $dom->loadHTML($this->tpl);
         $dom->inject($id, '<?=$' . $ctrl->uniqueID . ';?>', $mode);
         $this->tpl = $dom->getHTML();
      }
      $this->add($ctrl);
      $this->ajax->inject($ctrl->render(), $id, $mode, 0, true);
      return $this;
   }

   public function delete($id = null, $isRecursion = false)
   {
      if ($id === null)
      {
         $parent = $this->page->getByUniqueID($this->properties['parentUniqueID']);
         if (!$parent) throw new \Exception(err_msg('ERR_CTRL_6', array($this->getFullID())));
         return $parent->deleteByUniqueID($this->attributes['uniqueID']);
      }
      $ctrl = $this->get($id, $isRecursion);
      if ($ctrl === false) return false;
      return $this->page->getByUniqueID($ctrl->parentUniqueID)->deleteByUniqueID($ctrl->uniqueID);
   }

   public function deleteByUniqueID($uniqueID, $time = 0)
   {
      if (!isset($this->controls[$uniqueID])) return false;
      $ctrl = $this[$uniqueID];
      $this->tpl = str_replace('<?=$' . $ctrl->uniqueID . ';?>', '', $this->tpl);
      if ($ctrl instanceof IPanel)
      {
         foreach ($ctrl as $child) $ctrl->deleteByUniqueID($child->uniqueID, $time);
         unset($this->page->tpl[$ctrl->uniqueID]);
      }
      $ctrl->remove($time);
      unset($this->controls[$uniqueID]);
      unset($this->page[$uniqueID]);
      return $this;
   }

   public function replace($id, IWebControl $ctrl, $isRecursion = false)
   {
      $old = $this->get($id, $isRecursion);
      if ($old === false) return false;
      return $this->page->getByUniqueID($old->parentUniqueID)->replaceByUniqueID($old->uniqueID, $ctrl);
   }

   public function replaceByUniqueID($uniqueID, IWebControl $ctrl)
   {
      if (!isset($this->controls[$uniqueID])) return false;
      $first = $this[$uniqueID];
      if ($first instanceof IPanel)
      {
         unset($this->page->tpl[$first->uniqueID]);
         foreach ($first as $child) $first->deleteByUniqueID($child->uniqueID);
      }
      $params = $ctrl->getParameters();
      $params['parameters'][0]['uniqueID'] = $uniqueID;
      $params['parameters'][1]['parentUniqueID'] = $first->parentUniqueID;
      $ctrl->setParameters($params);
      $ctrl->update();
	  $this->page[$ctrl->uniqueID] = $ctrl;
      return $this;
   }

    /**
     * @param $id
     * @param bool $isRecursion
     * @return bool|WebControl|IPanel|PopUp
     */
    public function get($id, $isRecursion = false)
   {
      $cid = explode('.', $id);
      $controls = $this->controls;
      if (!$isRecursion)
      {
         foreach ($cid as $id)
         {
            if (is_array($controls))
            {
               $k = count($controls);
               foreach ($controls as $uniqueID => $v)
               {
                  $vs = $this->page->getActualVS($uniqueID);
                  if ($vs['parameters'][1]['id'] == $id) break;
                  $k--;
               }
               if ($k == 0) return false;
               $controls = $vs['controls'];
            }
            else return false;
         }
         return $this->page->getByUniqueID($uniqueID);
      }
      return $this->searchControl($cid, $this->controls);
   }

   public function getByUniqueID($uniqueID)
   {
      if (isset($this->controls[$uniqueID])) return $this->page->getByUniqueID($uniqueID);
      return false;
   }

   public function assign(array $values, $isRecursion = false, $isRecursionAssignment = false)
   {
      foreach ($values as $id => $value)
      {
         $ctrl = $this->get($id, $isRecursion);
         if ($ctrl && method_exists($ctrl, 'assign'))
         {
            if (!($ctrl instanceof IPanel))
              $ctrl->assign($value);
            else if ($isRecursionAssignment)
              $ctrl->assign($value, $isRecursion, $isRecursionAssignment);
         }
      }
      return $this;
   }

   public function CSS()
   {
      foreach ($this as $ctrl) $ctrl->CSS();
      return $this;
   }

   public function JS()
   {
      foreach ($this as $ctrl) $ctrl->JS();
      return $this;
   }

   public function HTML()
   {
      foreach ($this as $ctrl) $ctrl->HTML();
      return $this;
   }

   public function getXHTML()
   {
      $tag = strtolower(Utils\PHPParser::getClassName(get_class($this)));
      $xml = '<' . $tag . $this->getXHTMLParams() . '>';
      $temp = (string)$this->tpl;
      foreach ($this as $uniqueID => $ctrl) $temp = str_replace('<?=$' . $uniqueID . ';?>', $ctrl->getXHTML(), $temp);
      $xml .= $temp;
      return $xml .= '</' . $tag . '>';
   }

   public function render()
   {
      if ($this->properties['expire'] > 0 && !$this->isExpired()) return $this->reg->cache->get($this->properties['cacheID']);
      if (!$this->properties['visible']) $html = $this->invisible();
      else $html = '<' . $this->properties['tag'] . $this->getParams() . '>' . (($this->properties['visible']) ? $this->getInnerHTML() : '') . '</' . $this->properties['tag'] . '>';
      if ($this->properties['expire'] > 0) $this->reg->cache->set($this->properties['cacheID'], $html, $this->properties['expire']);
      return $html;
   }

   public function getInnerHTML()
   {
      foreach ($this as $uniqueID => $ctrl) $this->tpl->{$uniqueID} = $ctrl->render();
      return $this->tpl->render();
   }

   public function setParameters(array $parameters)
   {
      $this->controls = $parameters['controls'];
      return parent::setParameters($parameters);
   }

   public function getParameters()
   {
      return array('controls' => $this->controls) + parent::getParameters();
   }

   protected function searchControl(array $cid, array $controls)
   {
      foreach ($cid as $n => $id)
      {
         if (is_array($controls))
         {
            $k = count($controls);
            $panels = array();
            foreach ($controls as $uniqueID => $v)
            {
               $vs = $this->page->getActualVS($uniqueID);
               if ($vs['parameters'][1]['id'] == $id) break;
               if ($n == 0 && $vs['controls']) $panels[] = $vs['controls'];
               $k--;
            }
            if ($k == 0)
            {
               foreach ($panels as $controls)
               {
                  $ctrl = $this->searchControl($cid, $controls);
                  if ($ctrl !== false) return $ctrl;
               }
               return false;
            }
         }
         $controls = $vs['controls'];
      }
      return $this->page->getByUniqueID($uniqueID);
   }

   protected function repaint()
   {
      parent::repaint();
      foreach ($this as $ctrl) $ctrl->repaint();
   }
}

?>
