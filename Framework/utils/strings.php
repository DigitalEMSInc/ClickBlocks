<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * (viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: strings.php
 *
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Utils;

/**
 * Contains the set of static methods for facilitating the work with strings.
 *
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class Strings
{
   /**
    * Trimmed a large text.
    *
    * @param string $string large text.
    * @param integer $length length of the short text.
    * @param boolean $byWord trim to nearest word right.
    * @param boolean $stripTags delete HTML and PHP tags.
    * @param string $stripTagsExceptions tags, which must not be deleted.
    * @retun string short text
    * @access public
    * @static
    */
   public static function cut($string, $length, $byWord = true, $stripTags = false, $stripTagsExceptions = '')
   {
      if ($stripTags) $string = strip_tags($string, $stripTagsExceptions);
      if ($length < 4 || $string == '' || strlen($string) <= $length ) return $string;
      $lastSpacePos = strpos($string, ' ', $length - 1);
      if ($byWord)
      {
         if ($lastSpacePos === false) $shortText = $string;
         else $shortText = substr($string, 0, $lastSpacePos) . '...';
      }
      else
      {
         if ($lastSpacePos > $length || $lastSpacePos === false) $shortText = trim(substr($string, 0, $length - 3)) . '...';
         else $shortText = substr($string, 0, $lastSpacePos) . '...';
      }
      return $shortText;
   }

   /**
    * Highlights a substring inside the given string.
    *
    * @param string $value
    * @param string $text
    * @param string $l
    * @param string $r
    * @return string
    * @access public
    * @static
    */
   public static function highlight($value, $text, $l = '<b>', $r = '</b>')
   {
      return preg_replace('/(' . $value . ')/i', $l . '$1' . $r, $text);
   }

   /**
    * Returns "s" if the number ends on 1 and an empty string otherwise.
    *
    * @param integer $n
    * @return string
    * @access public
    * @static
    */
   public static function plural($n)
   {
      return $n % 10 == 1 && $n % 100 != 11 ? '' : 's';
   }

  public static function xmlspecialchars($text)
  {
    $text = str_replace('&', '&amp;', $text);
    $text = str_replace('<', '&lt;', $text);
    return $text;
  }


}

?>
