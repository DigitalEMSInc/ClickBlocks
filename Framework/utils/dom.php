<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2014  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * @copyright  2007-2014 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 */

namespace ClickBlocks\Utils;

use ClickBlocks\Core;

/**
 * The class is extended operations with the DOM in HTML document.
 *
 * @package cb.utils
 */
class DOMDocumentEx extends \DOMDocument
{
  /**
   * Error message templates.
   */
  const ERR_DOM_1 = 'DOM Element with ID = "[{var}]" is not found.';
  const ERR_DOM_2 = 'Injecting mode "[{var}]" is invalid.';

  /**
   * Injecting modes.
   */
  const DOM_INJECT_TOP = 'top';
  const DOM_INJECT_BOTTOM = 'bottom';
  const DOM_INJECT_AFTER = 'after';
  const DOM_INJECT_BEFORE = 'before';

  /**
   * Creates a new DOMDocumentEx object.
   * 
   * @param string $version - the version number of the document as part of the XML declaration.
   * @param string $charset - the encoding of the document as part of the XML declaration.
   * @access public
   */
  public function __construct($version = '1.0', $charset = 'UTF-8')
  {
    parent::__construct($version, $charset);
  }

  /**
   * The function parses the HTML contained in the string source. Unlike loading XML, HTML does not have to be well-formed to load.
   * 
   * @param string $source - the HTML string.
   * @param integer $options - bitwise OR of the libxml option constants.
   * @return boolean - returns TRUE on success or FALSE on failure.
   * @access public
   */
  public function loadHTML($source, $options = 0)
  {
    $enabled = \CB::isErrorHandlingEnabled();
    $level = \CB::errorHandling(false, E_ALL & ~E_NOTICE & ~E_WARNING);
    $charset = $this->encoding;
    $source = mb_convert_encoding($source, 'HTML-ENTITIES', $this->encoding);
    $res = parent::loadHTML($source, $options);
    $this->encoding = $charset;
    \CB::errorHandling($enabled, $level);
    return $res;
  }

  /**
   * The method parses the HTML document in the file named filename.
   * Unlike loading XML, HTML does not have to be well-formed to load.
   * 
   * @param string $filename - the path to the HTML file.
   * @param integer $options - bitwise OR of the libxml option constants.
   * @return boolean - returns TRUE on success or FALSE on failure.
   * @access public
   */
  public function loadHTMLFile($filename, $options = 0)
  {
    return $this->loadHTML(file_get_contents($filename), $options);
  }
  
  /**
   * Returns HTML of the give node.
   * The method returns HTML of the root node if $node is not defined.
   *
   * @param DOMNode $node - the given node.
   * @return string
   * @access public
   */
  public function getHTML(\DOMNode $node = null)
  {
    $node = $node ?: $this->documentElement;
    if (!$node) return '';
    if (!$node->parentNode) 
    {
      $parent = $this->createElement('root');
      $parent->appendChild($node);
      return $this->getInnerHTML($parent);
    }
    return $this->getInnerHTML($node->parentNode);
  }
  
  /**
   * Sets the HTML of the given node.
   * If $node is not defined HTML of the entire document will be set.
   *
   * @param DOMNode $node - the given node.
   * @return DOMNode - returns the given node object.
   * @access public
   */
  public function setHTML($html, \DOMNode $node = null)
  {
    $node = $node ?: $this->documentElement;
    if (!$node) 
    {
      $this->loadHTML('<html></html>');
      $node = $this->documentElement;
    }
    else if (!$node->parentNode)
    {
      return $this->HTMLToNode($html);
    }
    return $this->setInnerHTML($html, $node->parentNode);
  }
  
  /**
   * Returns the inner HTML of the given node.
   * The method returns inner HTML of the root node if $node is not defined.
   * 
   * @param DOMNode $node - the given node.
   * @return string 
   * @access public
   */
  public function getInnerHTML(\DOMNode $node = null)
  {
    $node = $node ?: $this->documentElement;
    if (!$node) return '';
    $html = '';
    foreach ($node->childNodes as $child)
    {
      $html .= $child->ownerDocument->saveHTML($child);
    }
    return $html;
  }

  /**
   * Sets the inner HTML of the given node.
   * If $node is not defined inner HTML of the root node will be set.
   * 
   * @param DOMNode $node - the given node.
   * @param string $html - the node inner HTML.
   * @return DOMNode - returns the given node object.
   * @access public
   */
  public function setInnerHTML($html, \DOMNode $node = null)
  {
    $node = $node ?: $this->documentElement;
    if (!$node) 
    {
      $this->loadHTML('<html></html>');
      $node = $this->documentElement;
    }
    $node->nodeValue = '';
    if ($node instanceof \DOMDocument) $node->removeChild($this->documentElement);
    else foreach ($node->childNodes as $child) $node->removeChild($child);
    $node->appendChild($this->HTMLToNode($html));
    return $node;
  }

  /**
   * Changes the inner HTML of the node.
   * if the node doesn't exist the exception will be thrown.
   * 
   * @param string|DOMNode $id - the node ID or node object.
   * @param string $html - the node inner HTML.
   * @return DOMNode - returns object of the changed node.
   * @access public
   */
  public function insert($id, $html)
  {
    $node = $id instanceof \DOMNode ? $id : $this->getElementById($id);
    if ($node === null) throw new Core\Exception($this, 'ERR_DOM_1', $id);
    return $this->setInnerHTML($html, $node);
  }

  /**
   * Replaces the node by the given HTML.
   * if the node doesn't exist the exception will be thrown.
   * 
   * @param string|DOMNode $id - the node ID or node object.
   * @param string $html - HTML for replacing.
   * @return DOMNode - returns object of the changed node.
   * @access public
   */
  public function replace($id, $html)
  {
    $node = $id instanceof \DOMNode ? $id : $this->getElementById($id);
    if ($node === null) throw new Core\Exception($this, 'ERR_DOM_1', $id);
    $node->parentNode->replaceChild($new = $this->HTMLToNode($html), $node);
    return $new;
  }

  /**
   * Adds HTML to the node.
   * The optional parameter specifies where the HTML will be added: at the beginning or end, before or after the node.
   * if the node doesn't exist the exception will be thrown.
   * 
   * @param string|DOMNode $id - the node ID or node object.
   * @param string $html - the HTML for adding.
   * @param string $mode - the injecting mode.
   * @return DOMNode - returns object of the changed node.
   * @access public
   */
  public function inject($id, $html, $mode = self::DOM_INJECT_TOP)
  {
    $old = $id instanceof \DOMNode ? $id : $this->getElementById($id);
    if ($old === null) throw new Core\Exception($this, 'ERR_DOM_1', $id);
    $node = $this->HTMLToNode($html);
    switch ($mode)
    {
      case self::DOM_INJECT_TOP:
        $old->firstChild ? $old->insertBefore($node, $old->firstChild) : $old->appendChild($node);
        return $old;
      case self::DOM_INJECT_BOTTOM:
        $old->appendChild($node);
        return $old;
      case self::DOM_INJECT_BEFORE:
        if ($old->parentNode) $old->parentNode->insertBefore($node, $old);
        return $old->parentNode;
      case self::DOM_INJECT_AFTER:
        if ($old->parentNode) $old->nextSibling ? $old->parentNode->insertBefore($node, $old->nextSibling) : $old->parentNode->appendChild($node);
        return $old->parentNode;
    }
    throw new Core\Exception($this, 'ERR_DOM_2', $mode);
  }

  /**
   * Converts the given HTML to the node object.
   * If HTML contains several elements only the first one will be converted to node.
   * 
   * @param string $html - the HTML for conversion.
   * @return DOMNode
   * @access public
   */
  public function HTMLToNode($html)
  {
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', $this->encoding);
    if (!preg_match('/\A<([^>? ]+)/', ltrim($html), $tag)) return new \DOMText($html);
    $tag = strtolower($tag[1]);
    $dom = new DOMDocumentEx($this->version, $this->encoding);
    $dom->loadHTML($html);
    if ($tag == 'html') $node = $dom->documentElement;
    else if ($tag == 'body' || $tag == 'head') $node = $dom->documentElement->firstChild;
    else $node = $dom->documentElement->firstChild->firstChild;
    return $this->importNode($node, true);
  }
   
  /**
   * Searches and returns an element by its ID.
   * Returns the DOMElement or NULL if the element is not found.
   * 
   * @param string $id - the unique ID of the element.
   * @return DOMElement
   * @access public
   */
  public function getElementById($id)
  {
    $el = parent::getElementById($id);
	   if ($el === null)
	   {
      $xp = new \DomXPath($this);
      $res = $xp->query('//*[@id = \'' . addslashes($id) . '\']'); 
      $el = $res->item(0); 
	   }
	   return $el;
  }
}