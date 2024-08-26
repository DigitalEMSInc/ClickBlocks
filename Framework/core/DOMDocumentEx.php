<?php

namespace ClickBlocks\Core;

use ClickBlocks\Web;
use DOMNode;
use ReturnTypeWillChange;

class DOMDocumentEx extends \DOMDocument
{
   const DOM_INJECT_TOP = 'top';
   const DOM_INJECT_BOTTOM = 'bottom';
   const DOM_INJECT_AFTER = 'after';
   const DOM_INJECT_BEFORE = 'before';

   public function __construct($version = '1.0', $charset = null)
   {
      parent::__construct($version, ($charset) ?: Register::getInstance()->config->charset);
   }

   public function loadHTML($source, $options = 0): bool
   {
      Debugger::setErrorReporting(E_ALL & ~E_NOTICE & ~E_WARNING);
      $res = parent::loadHTML(Web\XHTMLParser::encodePHPTags($source));
      Debugger::setErrorReporting(E_ALL & ~E_NOTICE);
      return $res;
   }

   public function loadHTMLFile($filename, $options = 0): bool
   {
      Debugger::setErrorReporting(E_ALL & ~E_NOTICE & ~E_WARNING);
      $res = $this->loadHTML(file_get_contents($filename), $options);
      Debugger::setErrorReporting(E_ALL & ~E_NOTICE);
      return $res;
   }

   public function saveHTML(DOMNode $node = null): false|string
   {
      return Web\XHTMLParser::decodePHPTags(parent::saveHTML());
   }

   public function saveHTMLFile($filename): int|false
   {
      return file_put_contents($filename, $this->saveHTML());
   }

   public function getHTML(): string
   {
      return $this->getInnerHTML($this->documentElement->firstChild);
   }

   public function insert($id, $html): void
   {
      $el = $this->getElementById($id);
      if ($el == null) throw new \Exception(err_msg('ERR_DOM_1', array($id)));
      $this->setInnerHTML($el, $html);
   }

   public function replace($id, $html): void
   {
      $el = $this->getElementById($id);
      if ($el == null) throw new \Exception(err_msg('ERR_DOM_1', array($id)));
      $el->parentNode->replaceChild($this->HTMLToNode($html), $el);
   }

   public function inject($id, $html, $mode = self::DOM_INJECT_TOP): void
   {
      $el = $this->getElementById($id);
      if ($el == null) throw new \Exception(err_msg('ERR_DOM_1', array($id)));
      $node = $this->HTMLToNode($html);
      switch ($mode)
      {
         case self::DOM_INJECT_TOP:
           ($el->firstChild) ? $el->insertBefore($node, $el->firstChild) : $el->appendChild($node);
           break;
         case self::DOM_INJECT_BOTTOM:
           $el->appendChild($node);
           break;
         case self::DOM_INJECT_BEFORE:
           if ($el->parentNode) $el->parentNode->insertBefore($node, $el);
           break;
         case self::DOM_INJECT_AFTER:
           if ($el->parentNode) ($el->nextSibling) ? $el->parentNode->insertBefore($node, $el->nextSibling) : $el->parentNode->appendChild($node);
           break;
      }
   }

   public function getInnerHTML(DOMNode $node): string
   {
      foreach ($node->childNodes as $child)
      {
         $dom = new \DOMDocument();
         $dom->appendChild($dom->importNode($child, true));
         $html .= trim($dom->saveHTML());
      }
      return Web\XHTMLParser::decodePHPTags($html);
   }

   public function setInnerHTML(DOMNode $node, $html): void
   {
      $node->nodeValue = '';
      foreach ($node->childNodes as $child) $node->removeChild($child);
      $node->appendChild($this->HTMLToNode($html));
   }

   public function HTMLToNode($html): DOMNode|\DOMText|false
   {
      if ($html == '') return new \DOMText('');
      $dom = new DOMDocumentEx('1.0', $this->encoding);
      $dom->loadHTML($html);
      $node = $this->importNode($dom->documentElement->firstChild->firstChild, true);
      if (!preg_match('/^<[a-zA-Z].*/', $html)) $node = new \DOMText(Web\XHTMLParser::encodePHPTags($html));
      return $node;
   }
}
