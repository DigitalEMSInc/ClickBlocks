<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
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
 * Responsibility of this file: uri.php
 *
 * @category   Utils
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Utils;

/**
 * This class allows you to disassemble and assemble some URI
 *
 * @category   Utils
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class URI
{
   /**
    * The scheme of an URI.
    *
    * @var    string
    * @access public
    */
   public $scheme = null;

   /**
    * The source of an URI.
    * The source represents associative array of the following structure: array('port' => ..., 'host' => ..., 'user' => ..., 'pass' => ...)
    *
    * @var    array
    * @access public
    */
   public $source = array();

   /**
    * The path component of an URI.
    *
    * @var    array
    * @access public
    */
   public $path = array();

   /**
    * The query component of an URI.
    *
    * @var    array
    * @access public
    */
   public $query = array();

   /**
    * The fragment of an URI
    *
    * @var    string
    * @access public
    */
   public $fragment = null;

   /**
    * Constructs a new URI.
    *
    * - new URI() parses current URI of a page.
    *
    * - new URI($uri) parses given URI - $uri.
    *
    * @param string $uri
    * @access public
    */
   public function __construct($uri = null)
   {
      if ($uri === null) $uri = (substr($_SERVER['SERVER_PROTOCOL'], 0, 5) == 'HTTPS' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $this->parse($uri);
   }

   /**
    * Returns URI.
    *
    * @return string
    * @access public
    */
   public function getURI()
   {
      switch ($this->scheme)
      {
         case 'http':
         case 'https':
           return $this->scheme . (($this->scheme) ? '://' : '') . $this->getSource() . $this->getQueryString();
      }
   }

   /**
    * Returns the query component of an URI
    *
    * @return string
    * @access public
    */
   public function getQueryString()
   {
      switch ($this->scheme)
      {
         case 'http':
         case 'https':
           $query = $this->getQuery();
           return $this->getPath() . (($query) ? '?' . $query : '') . (($this->fragment) ? '#' . $this->fragment : '');
      }
   }

   /**
    * Returns the source component of an URI
    *
    * @return string
    * @access public
    */
   public function getSource()
   {
      switch ($this->scheme)
      {
         case 'http':
         case 'https':
           $source = (($this->source['user']) ? $this->source['user'] . ':' . $this->source['pass'] : '');
           return (($source) ? $source . '@' : '') . $this->source['host'] . (($this->source['port'] && $this->source['port'] != 80) ? ':' . $this->source['port'] : '');
      }
   }

   /**
    * Returns the path component of an URI
    *
    * @return string
    * @access public
    */
   public function getPath()
   {
      switch ($this->scheme)
      {
         case 'http':
         case 'https':
           $path = (array)$this->path;
           foreach ($path as &$part) $part = urlencode($part);
           $path = implode('/', $path);
           return ($path[0] == '/') ? $path : '/' . $path;
      }
   }

   /**
    * Returns the query component of an URI
    *
    * @return string
    * @access public
    */
   public function getQuery()
   {
      switch ($this->scheme)
      {
         case 'http':
         case 'https':
           return http_build_query($this->query);
      }
   }

   /**
    * Parsing of some URI
    *
    * @param string $uri
    * @access public
    */
   public function parse($uri)
   {
      preg_match_all('@^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?@', $uri, $arr);
      $this->scheme = strtolower($arr[2][0]);
      $this->source = array();
      switch ($this->scheme)
      {
         case 'http':
         case 'https':
           $data = explode('@', $arr[4][0]);
           if (!$data[1])
           {
              $data = explode(':', $data[0]);
              $this->source['port'] = $data[1];
              $this->source['host'] = urldecode($data[0]);
              $this->source['user'] = '';
              $this->source['pass'] = '';
           }
           else
           {
              $d1 = explode(':', $data[1]);
              $this->source['port'] = $d1[1];
              $this->source['host'] = urldecode($d1[0]);
              $d2 = explode(':', $data[0]);
              $this->source['pass'] = urldecode($d2[1]);
              $this->source['user'] = urldecode($d2[0]);
           }
           break;
      }
      $this->path = ($arr[5][0] != '') ? explode('/', $arr[5][0]) : array();
      if (count($this->path) > 1) array_shift($this->path);
      foreach ($this->path as &$part) $part = urldecode($part);
      $this->query = array();
      if ($arr[7][0] != '') foreach (explode('&', urldecode($arr[7][0])) as $param)
      {
         $data = explode('=', $param);
         $this->query[$data[0]] = $data[1];
      }
      $this->fragment = urldecode($arr[9][0]);
   }
}

?>
