<?php

namespace ClickBlocks\Web;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\POM;

class XHTMLParser
{
   protected $reg = null;
   protected $parser = null;
   protected $tpl = null;
   protected $res = null;
   protected $stack = null;
   protected $parentID = null;
   protected $subjectID = null;
   protected $autoClosedTags = array('br' => 1, 'hr' => 1, 'img' => 1, 'input' => 1);
   protected static $validators = array();
   protected static $isParsing = false;

   public static $controls = array('BODY' => 1, 'PANEL' => 1, 'WEBFORM' => 1, 'REPEATER' => 1, 'CHECKBOXGROUP' => 1, 'RADIOBUTTONGROUP' => 1,
                                   'TEMPLATE' => 1, 'HIDDEN' => 1, 'UPLOAD' => 1, 'UPLOADBUTTON' => 1, 'TEXTLABEL' => 1, 'TEXTBOX' => 1, 'AUTOFILL' => 1,
                                   'PASSWORD' => 1, 'MEMO' => 1, 'IMAGE' => 1, 'TEXTBUTTON' => 1, 'IMAGEBUTTON' => 1, 'HYPERLINK' => 1, 'CHECKBOX' => 1,
                                   'RADIOBUTTON' => 1, 'SWITCHGROUP' => 1, 'NAVIGATOR' => 1, 'SQLDROPDOWNBOX' => 1, 'DROPDOWNBOX' => 1, 'VALIDATOR' => 1, 'WIDGET' => 1,
                                   'TIMEPICKER' => 1, 'DATEPICKER' => 1, 'TINYMCE' => 1, 'COLORPICKER' => 1, 'IMGEDITOR' => 1, 'LOGIN' => 1, 'DATETIMEPICKER' => 1);

   public static function encodePHPTags($str)
   {
      return strtr($str, array('<?' => 'b5a6ed8b4b898878e1aa65eba3ef089d', '?>' => '6ec256c8a7669df51b63aea2878825e2', '&' => '6cff047854f19ac2aa52aac51bf3af4a'));
   }

   public static function decodePHPTags($str)
   {
      return strtr($str, array('b5a6ed8b4b898878e1aa65eba3ef089d' => '<?', '6ec256c8a7669df51b63aea2878825e2' => '?>', '6cff047854f19ac2aa52aac51bf3af4a' => '&'));
   }

   public static function exe($code)
   {
      $config = Core\Register::getInstance()->config;
      ob_start();
      Core\Debugger::setEvalCode($code);
      if (eval(' ?>' . $code . '<?php ') === false) Core\Debugger::setEvalCode($code, true);
      $res = ob_get_contents();
      ob_end_clean();
      return $res;
   }

   public static function evil($code)
   {
      if ($code == '') return;
      $code = '$tmp = ' . $code . ';';
      Core\Debugger::setEvalCode($code);
      if (eval($code) === false) Core\Debugger::setEvalCode($code, true);
      return $tmp;
   }

   public function __construct()
   {
      $this->reg = Core\Register::getInstance();
      $this->parser = xml_parser_create($this->reg->config->charset);
      xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
      xml_set_element_handler($this->parser, array($this, 'start'), array($this, 'end'));
      xml_set_character_data_handler($this->parser, array($this, 'cdata'));
      xml_set_default_handler($this->parser, array($this, 'cdata'));
   }

   public function __destruct()
   {
      xml_parser_free($this->parser);
   }

   public static function isParsing()
   {
      return self::$isParsing;
   }

   public function getValidators()
   {
      return self::$validators;
   }

   public function parse($file, &$template, $adjustValidators = true)
   {
      $xhtml = (strlen($file)<=4096 && is_file($file)) ? file_get_contents($file) : $file;
      $oldParsing = self::$isParsing;
      if (!self::$isParsing) self::$validators = array();
      self::$isParsing = true;
      $this->stack = new \SplStack();
      $this->parentID = $this->subjectID = $this->res = null;
      $this->tpl = ($template instanceof Core\Template) ? $template : new Core\Template();
      if (!xml_parse($this->parser, self::encodePHPTags($xhtml))) throw new \Exception(err_msg('ERR_XHTML_1', array(xml_error_string(xml_get_error_code($this->parser)), (strlen($file)) ? $file : htmlspecialchars($xhtml), xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser))));
      $template = $this->tpl;
      self::$isParsing = $oldParsing;
      if (!self::$isParsing && $adjustValidators)
      {
         if (!$this->reg->page->body) $this->reg->page->xhtml->body = $this->res;
         $this->adjustValidators();
      }
      return $this->res;
   }

   public function adjustValidators()
   {
      foreach (self::$validators as $uniqueID)
      {
         $validator = $this->reg->page->getByUniqueID($uniqueID);
         if (!is_array($validator->controls))
         {
            $validator->__set('controls', $validator->controls);
            $this->reg->page[$uniqueID] = $validator;
         }
      }
   }

   protected function start($parser, $tg, array $attributes)
   {
      $tag = strtoupper($tg);
      if (substr($tag, 0, 6) == 'WIDGET') $tag = 'WIDGET';
      else if (substr($tag, 0, 9) == 'VALIDATOR') $tag = 'VALIDATOR';
      if (isset(self::$controls[$tag]))
      {
         if ($tag == 'TEMPLATE')
         {
            $file = Core\IO::dir(self::exe(self::decodePHPTags($attributes['path'])));
            if ($attributes['disableException'] && !is_file($file)) return;
            unset($attributes['path']);
            unset($attributes['disableException']);
            $template = '<panel id="' . uniqid('c') . '">' . file_get_contents($file) . '</panel>';
            $obj = foo(new self())->parse($template, $template);
            if (count($this->stack) > 0)
            {
               $ctrl = $this->stack->top();
               foreach ($obj as $child)
               {
                  $params = $child->getParameters();
                  $params['parameters'][1]['parentUniqueID'] = '';
                  $child->setParameters($params);
                  $ctrl->add($child);
                  $this->copyTemplates($ctrl, $child, $template);
               }
               $this->tpl[$ctrl->uniqueID] .= (string)$template[$obj->uniqueID];
               unset($this->reg->page[$obj->uniqueID]);
            }
            else
            {
               $this->tpl = $template;
               $this->stack->push($obj);
            }
         }
         else
         {
            $class = '\ClickBlocks\Web\UI\POM\\' . $tg;
            $ctrl = new $class($attributes['id']);
            if ($ctrl instanceof POM\IPanel)
            {
               if ($attributes['masterpage'])
               {
                  if (count($this->stack) > 0) throw new \Exception(err_msg('ERR_XHTML_5', array(get_class($this), $ctrl->id)));
                  $attributes['masterpage'] = self::exe(self::decodePHPTags($attributes['masterpage']));
                  $this->stack->push(foo(new self())->parse(Core\IO::dir($attributes['masterpage']), $this->tpl));
                  $this->parentID = self::exe(self::decodePHPTags($attributes['parentID']));
                  $this->subjectID = $ctrl->uniqueID;
                  unset($attributes['masterpage']);
                  unset($attributes['parentID']);
               }
            }
            $this->stack->push($ctrl);
            $ctrl->parse($attributes, $this->tpl);
            foreach ($attributes as $k => $v) $ctrl->__set($k, $v);
            if ($ctrl instanceof POM\IValidator) self::$validators[] = $ctrl->uniqueID;
         }
      }
      else
      {
         if (!count($this->stack)) throw new \Exception(err_msg('ERR_XHTML_2'));
         $html = '<' . $tg;
         if (count($attributes))
         {
            $tmp = array();
            foreach ($attributes as $k => $v) $tmp[] = $k . '="' . $v . '"';
            $html .= ' ' . implode(' ', $tmp);
         }
         if ($this->autoClosedTags[$tg]) $html .= ' />';
         else $html .= '>';
         $ctrl = $this->stack->top();
         if ($ctrl instanceof POM\IPanel) $this->tpl[$ctrl->uniqueID] .= self::decodePHPTags($html);
         else if (isset($ctrl->text)) $ctrl->text .= self::exe(self::decodePHPTags($html));
      }
   }

   protected function cdata($parser, $content)
   {
      $ctrl = $this->stack->top();
      if ($ctrl instanceof POM\IPanel) $this->tpl[$ctrl->uniqueID] .= self::decodePHPTags($content);
      else if (isset($ctrl->text)) $ctrl->text .= self::exe(self::decodePHPTags($content));
   }

   protected function end($parser, $tg)
   {
      $tag = strtoupper($tg);
      if (substr($tag, 0, 6) == 'WIDGET') $tag = 'WIDGET';
      else if (substr($tag, 0, 9) == 'VALIDATOR') $tag = 'VALIDATOR';
      if (isset(self::$controls[$tag]))
      {
         if ($tag == 'TEMPLATE') return;
         $ctrl = $this->stack->pop();
         if ($ctrl->uniqueID == $this->subjectID)
         {
            if ($this->parentID)
            {
               $parent = $this->stack->top()->get($this->parentID, true);
               if ($parent === false) throw new \Exception(err_msg('ERR_XHTML_3', array($this->parentID)));
            }
            if (!$parent) $parent = $this->stack->top();
            $this->tpl[$parent->uniqueID] = preg_replace('/<\?=[\s]?\$' . $ctrl->id . '[\s;]?\?>/', '<?=$' . $ctrl->uniqueID . ';?>', $this->tpl[$parent->uniqueID]);
            if (!($parent instanceof POM\IPanel)) throw new \Exception(err_msg('ERR_XHTML_4', array($this->parentID, get_class($ctrl))));
            $parent->add($ctrl);
            $this->reg->page[$parent->uniqueID] = $parent;
            $ctrl = $this->stack->pop();
            $this->subjectID = $this->parentID = null;
         }
         else if (count($this->stack) > 0)
         {
            $parent = $this->stack->top();
            $parent->add($ctrl);
            $this->tpl[$parent->uniqueID] .= '<?=$' . $ctrl->uniqueID . ';?>';
         }
         if (count($this->stack) < 1)
         {
            $this->reg->page[$ctrl->uniqueID] = $ctrl;
            $this->res = $ctrl;
         }
      }
      else if (!$this->autoClosedTags[$tg])
      {
         $ctrl = $this->stack->top();
         if ($ctrl instanceof POM\IPanel) $this->tpl[$ctrl->uniqueID] .= '</' . $tg . '>';
         else if (isset($ctrl->text)) $ctrl->text .= '</' . $tg . '>';
      }
   }

   private function copyTemplates($ctrl, $child, Core\ITemplate $template)
   {
      if ($child instanceof POM\IPanel)
      {
         $this->tpl->setTemplate($child->uniqueID, (string)$template[$child->uniqueID], $ctrl->uniqueID);
         foreach ($child as $sib) $this->copyTemplates($child, $sib, $template);
      }
   }
}
