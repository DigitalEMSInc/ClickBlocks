<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web;

class Navigator extends WebControl
{
   public function __construct($id, $pos = 0, $count = 0)
   {
      parent::__construct($id);
      $this->properties['callBack'] = null;
      $this->properties['pos'] = $pos;
      $this->properties['last'] = 0;
      $this->properties['count'] = $count;
      $this->properties['pageSize'] = 10;
      $this->properties['navSize'] = 3;      // [2 * $navSize + 1] -- number of shown page numbers
      $this->properties['text'] = null;
      $this->properties['tag'] = 'div';
   }

   public function parse(array &$attributes, Core\ITemplate $tpl)
   {
      if ($attributes['template'])
      {
         $template = Web\XHTMLParser::exe(Web\XHTMLParser::decodePHPTags($attributes['template']));
         $attributes['text'] = is_file(Core\IO::dir($template)) ? file_get_contents(Core\IO::dir($template)) : $template;
         unset($attributes['template']);
      }
      return parent::parse($attributes, $tpl);
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $this->normalizeProperties();
      if ($this->properties['count'] <= $this->properties['pageSize']) $html = '';
      else
      {
         $tokens = $this->getTokens($html, $type);
         if ($type == 'pspapsp')
         {
            if ($this->properties['last'] <= 4 * $this->properties['navSize'])
            {
               $html = str_replace('@spacer@', '', $html);
               $html = preg_replace('/@page@/', '', $html, 1);
               $html = $this->replacePages($tokens, $html, 0, $this->properties['last'], true);
            }
            else if ($this->properties['pos'] <= 2 * $this->properties['navSize'])
            {
               $html = preg_replace('/@spacer@/', '', $html, 1);
               $html = preg_replace('/@page@/', '', $html, 1);
               $html = str_replace('@spacer@', $tokens['spacer'], $html);
               $html = $this->replacePages($tokens, $html, 0, 3 * $this->properties['navSize'], true);
               $html = $this->replacePagesRight($tokens, $html, $this->properties['last'] - $this->properties['navSize'] + 1);
            }
            else if ($this->properties['pos'] >= $this->properties['last'] - 2 * $this->properties['navSize'])
            {
               $html = preg_replace('/@spacer@/', $tokens['spacer'], $html, 1);
               $html = str_replace('@spacer@', '', $html);
               $html = $this->replacePagesLeft($tokens, $html, $this->properties['navSize']);
               $html = $this->replacePages($tokens, $html, $this->properties['last'] - 3 * $this->properties['navSize'], $this->properties['last'], true);
            }
            else
            {
               $html = str_replace('@spacer@', $tokens['spacer'], $html);
               $html = $this->replacePagesLeft($tokens, $html, $this->properties['navSize']);
               $html = $this->replacePages($tokens, $html, $this->properties['pos'] - $this->properties['navSize'], $this->properties['pos'] + $this->properties['navSize'], true);
               $html = $this->replacePagesRight($tokens, $html, $this->properties['last'] - $this->properties['navSize'] + 1);
            }
         }
         else if ($type == 'spaps')
         {
            if ($this->properties['last'] <= 2 * $this->properties['navSize'])
            {
               $html = str_replace('@spacer@', '', $html);
               $k1 = 0; $k2 = $this->properties['last'];
            }
            else if ($this->properties['pos'] <= $this->properties['navSize'])
            {
               $html = preg_replace('/@spacer@/', '', $html, 1);
               $html = str_replace('@spacer@', $tokens['spacer'], $html);
               $k1 = 0;
               $k2 = 2 * $this->properties['navSize'];
            }
            else if ($this->properties['pos'] >= $this->properties['last'] - $this->properties['navSize'])
            {
               $html = preg_replace('/@spacer@/', $tokens['spacer'], $html, 1);
               $html = str_replace('@spacer@', '', $html);
               $k1 = $this->properties['last'] - 2 * $this->properties['navSize'];
               $k2 = $this->properties['last'];
            }
            else
            {
               $html = str_replace('@spacer@', $tokens['spacer'], $html);
               $k1 = $this->properties['pos'] - $this->properties['navSize'];
               $k2 = $this->properties['pos'] + $this->properties['navSize'];
            }
            $html = $this->replacePages($tokens, $html, $k1, $k2, true);
         }
         else if ($type == 'paps' || $type == 'aps')
         {
            $flag = true;
            if ($this->properties['pos'] >= $this->properties['last'] - $this->properties['navSize'])
            {
               $html = preg_replace('/@spacer@/', '', $html, 1);
               $k2 = $this->properties['last'];
            }
            else
            {
               $html = preg_replace('/@spacer@/', $tokens['spacer'], $html, 1);
               $k2 = $this->properties['pos'] + $this->properties['navSize'];
            }
            if ($type == 'aps')
            {
               $k1 = $this->properties['pos'];
               $flag = false;
            }
            else $k1 = 0;
            $html = $this->replacePages($tokens, $html, $k1, $k2, $flag);
         }
         else if ($type == 'spap' || $type == 'spa')
         {
            if ($this->properties['pos'] <= $this->properties['navSize'])
            {
               $html = preg_replace('/@spacer@/', '', $html, 1);
               $k1 = 0;
            }
            else
            {
               $html = preg_replace('/@spacer@/', $tokens['spacer'], $html, 1);
               $k1 = $this->properties['pos'] - $this->properties['navSize'];
            }
            if ($type == 'spa') $k2 = $this->properties['pos'];
            else $k2 = $this->properties['last'];
            $html = $this->replacePages($tokens, $html, $k1, $k2, true);
         }
         else if ($type == 'pap')
         {
            $html = $this->replacePages($tokens, $html, 0, $this->properties['last'], true);
         }
         else if ($type == 'ap')
         {
            $html = $this->replacePages($tokens, $html, $this->properties['pos'], $this->properties['last'], false);
         }
         else if ($type == 'pa')
         {
            $html = $this->replacePages($tokens, $html, 0, $this->properties['pos'], true);
         }
         else
         {
            throw new \Exception(err_msg('ERR_CTRL_10', array($this->getFullID(), $type)));
         }
         if ($this->properties['pos'] < 1)
         {
            $html = strtr($html, array('@first@' => '', '@previous@' => ''));
         }
         else
         {
            $tokens['first'] = strtr($tokens['first'], array('#pos#' => 0, '#page#' => 1));
            $tokens['previous'] = strtr($tokens['previous'], array('#pos#' => $this->properties['pos'] - 1, '#page#' => $this->properties['pos']));
            $html = strtr($html, array('@first@' => $tokens['first'], '@previous@' => $tokens['previous']));
         }
         if ($this->properties['pos'] >= $this->properties['last'])
         {
            $html = strtr($html, array('@last@' => '', '@next@' => ''));
         }
         else
         {
            $tokens['last'] = strtr($tokens['last'], array('#pos#' => $this->properties['last'], '#page#' => $this->properties['last'] + 1));
            $tokens['next'] = strtr($tokens['next'], array('#pos#' => $this->properties['pos'] + 1, '#page#' => $this->properties['pos'] + 2));
            $html = strtr($html, array('@last@' => $tokens['last'], '@next@' => $tokens['next']));
         }
         $html = strtr($html, array('@page@' => '',
                                    '#count#' => $this->properties['count'],
                                    '#size#' => $this->properties['pageSize'],
                                    '#callBack#' => $this->properties['callBack'],
                                    '#uniqueID#' => $this->attributes['uniqueID']));
      }
      return '<' . $this->properties['tag'] . $this->getParams() . '>' . Web\XHTMLParser::decodePHPTags($html) . '</' . $this->properties['tag'] . '>';
   }

   public function normalizeProperties()
   {
      $this->properties['count'] = (int)$this->properties['count'];
      $this->properties['pageSize'] = (int)$this->properties['pageSize'];
      $this->properties['navSize'] = (int)$this->properties['navSize'];
      $this->properties['pos'] = (int)$this->properties['pos'];
      if ($this->properties['count'] == 0) $this->properties['count'] = 1;
      if ($this->properties['pageSize'] < 1) $this->properties['pageSize'] = 10;
      if ($this->properties['navSize'] < 1) $this->prperties['navSize'] = 3;
      $this->properties['last'] = ceil($this->properties['count'] / $this->properties['pageSize']) - 1;
      if ($this->properties['pos'] < 0) $this->properties['pos'] = 0;
      if ($this->properties['pos'] > $this->properties['last']) $this->properties['pos'] = $this->properties['last'];
   }

   protected function getTokens(&$html, &$type)
   {
      $parser = xml_parser_create($this->reg->config->charset);
      xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
      if (!xml_parse_into_struct($parser, '<root>' . Web\XHTMLParser::encodePHPTags($this->properties['text']) . '</root>', $struct, $index))
      {
         throw new \Exception(err_msg('ERR_XHTML_1', array(xml_error_string(xml_get_error_code($parser)), $this->properties['text'], xml_get_current_line_number($parser), xml_get_current_column_number($parser))));
      }
      xml_parser_free($parser);
      $tokens = array();
      while (($row = next($struct)) !== false)
      {
         $tag = strtolower($row['tag']);
         if ($tag == 'first' || $tag == 'last' || $tag == 'previous' || $tag == 'next' || $tag == 'page' || $tag == 'spacer')
         {
            if ($row['type'] == 'close') continue;
            $index = $tag;
            if ($tag == 'page')
            {
               if ($row['attributes']['type'] == 'active')
               {
                  $type .= 'a';
                  $index = 'active';
               }
               else $type .= 'p';
            }
            if ($tag == 'spacer') $type .= 's';
            if ($row['type'] == 'complete')
            {
               $html .= '@' . (($index == $tag) ? $tag : $index) . '@';
               if ($row['value'] != '') $tokens[$index] .= $row['value'];
               continue;
            }
            if ($row['type'] == 'open')
            {
               $html .= '@' . (($index == $tag) ? $tag : $index) . '@';
               while (1)
               {
                  $row = next($struct);
                  if (strcasecmp($row['tag'], $tag) == 0 && $row['type'] != 'cdata') break;
                  $tokens[$index] .= $this->getTagHTML($row);
               }
               prev($struct);
            }
            else
            {
               $tokens[$index] .= $this->getTagHTML($row);
            }
         }
         else
         {
            if ($tag == 'root' && ($row['type'] == 'open' || $row['type'] == 'close')) continue;
            $html .= $this->getTagHTML($row);
         }
      }
      return $tokens;
   }

   private function getTagHTML($row)
   {
      $tag = strtolower($row['tag']);
      if ($row['type'] == 'open' || $row['type'] == 'complete')
      {
         $html = '<' . $tag;
         if (is_array($row['attributes']) && count($row['attributes']))
         {
            $tmp = array();
            foreach ($row['attributes'] as $k => $v) $tmp[] = $k . '="' . $v . '"';
            $html .= ' ' . implode(' ', $tmp);
         }
         if ($row['type'] == 'complete')
         {
            if ($row['value'] == '') $html .= ' />';
            else $html .= '>' . $row['value'] . '</' . $tag . '>';
         }
         else $html .= '>' . $row['value'];
      }
      else if ($row['type'] != 'cdata')
      {
         $html = '</' . $tag . '>';
      }
      else
      {
         $html = $row['value'];
      }
      return $html;
   }

   private function replacePages(array $tokens, &$html, $k1, $k2, $flag)
   {
      for ($i = $k1; $i <= $k2; $i++)
      {
         if ($i == $this->properties['pos'])
         {
            $tokens['active'] = strtr($tokens['active'], array('#pos#' => $i, '#page#' => $i + 1));
            if ($flag) $html = preg_replace('/@page@/', '', $html, 1);
            $html = str_replace('@active@', $tokens['active'], $html);
         }
         else
         {
            $page = strtr($tokens['page'], array('#pos#' => $i, '#page#' => $i + 1));
            $html = preg_replace('/@page@/', $page . '@page@', $html, 1);
         }
      }
      return $html;
   }

   private function replacePagesLeft(array $tokens, &$html, $k2)
   {
      for ($i = 0; $i < $k2; $i++)
      {
         $page = strtr($tokens['page'], array('#pos#' => $i, '#page#' => $i + 1));
         $html = preg_replace('/@page@/', $page . '@page@', $html, 1);
      }
      $html = preg_replace('/@page@/', '', $html, 1);
      return $html;
   }

   private function replacePagesRight(array $tokens, &$html, $k1)
   {
      $html = preg_replace('/@page@/', '', $html, 1);
      for ($i = $k1; $i <= $this->properties['last']; $i++)
      {
         $page = strtr($tokens['page'], array('#pos#' => $i, '#page#' => $i + 1));
         $html = preg_replace('/@page@/', $page . '@page@', $html, 1);
      }
      return $html;
   }
}

?>
