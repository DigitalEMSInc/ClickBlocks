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
 * Responsibility of this file: phpparser.php 
 * 
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0        
 */ 

namespace ClickBlocks\Utils;

/**
 * Contains the set of static methods for manipulations with php code.
 *                                                                    
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class PHPParser
{                      
   /**
    * Seeks the occurrence of a php-code in other php-code.
    * Returns a numeric array of two elements. 
    * The first element is the number of the first token in the string to search. 
    * The second element is the number of the last token in the string to search.
    * 
    * @param string $needle
    * @param string $haystack  
    * @return array|boolean returns FALSE if php-fragment not found.
    * @access public
    * @static                          
    */                    
   public static function posInCode($needle, $haystack)
   {
      $tokens['needle'] = self::getTokens(trim($needle));
      $tokens['haystack'] = self::getTokens(trim($haystack));
      $i = $j = 0; 
      $start = $end = -1;
      do
      {
         $needle = self::getNextToken($tokens['needle'], $j);
         if ($needle === false) break;
         $haystack = self::getNextToken($tokens['haystack'], $i);
         if ($haystack === false) break;
         if (self::isEqual($haystack, $needle))
         {
            if ($start < 0) $start = $i - 1;
            $end = $i - 1;
         }
         else 
         {
            $start = $end = -1; 
            $j = 0;
         }
         $k++;
      }
      while (1);
      if ($end > 0 && $needle === false) return array($start, $end);
      return false;
   }
   
   /**
    * Checks checks whether or not containing a php-code to other php-code.
    * 
    * @param string $needle
    * @param string $haystack
    * @return boolean
    * @static                    
    */       
   public static function inCode($needle, $haystack)
   {
      return (self::posInCode($needle, $haystack) !== false);
   }
   
   /**
    * Replaces a php-code an other php-code.
    * 
    * @param string $search
    * @param string $replace
    * @param string $subject 
    * @return string
    * @access public
    * @static                           
    */       
   public static function replaceCode($search, $replace, $subject)
   {
      if (($pos = self::posInCode($search, $subject)) === false) return $subject;       
      $tockens = self::getTokens($subject);
      for ($i = 0; $i < count($tokens); $i++)
      {
         if ($i == $pos[0])
         { 
            $i = $pos[1] + 1;
            $code .= $replace; 
         }
         $code .= (is_array($tokens[$i])) ? $tokens[$i][1] : $tokens[$i];
      }  
      return $code;
   }
   
   /**
    * Removes a php-fragment from a php-code string.
    * 
    * @param string $search
    * @param string $subject
    * @return string
    * @access public
    * @static                       
    */       
   public static function removeCode($search, $subject)
   {
      return self::replaceCode($search, '', $subject);
   }
   
   /**
    * Returns the name of a class without its namespace name. 
    * 
    * @param string $class   
    * @return string         
    */       
   public static function getClassName($class)
   {
      if (is_object($class)) $class = get_class($class);
      $k = strrpos($class, '\\');
      if ($k !== false) $class = substr($class, $k + 1);
      return $class;
   }
   
   /**
    * Returns the array of tokens of a php-code string.
    * 
    * @param string $code
    * @return array
    * @access public
    * @static                    
    */       
   public static function getTokens($code)
   {
      return token_get_all('<?php ' . $code . ' ?>');
   }
   
   public static function getFullClassNames($file)
   {
      $tmp = array();
      $tokens = self::getTokens(is_file($file) ? file_get_contents($file) : $file);
      foreach ($tokens as $n => $token)
      {
         if ($token[0] == T_NAMESPACE) 
         {
            $i = $n;
            do
            {
               $tkn = $tokens[++$i];
               if ($tkn[0] == T_STRING || $tkn[0] == T_NS_SEPARATOR) $namespace .= $tkn[1];
            }
            while ($tkn != ';');
            $namespace .= '\\';
         }
         else if ($token[0] == T_CLASS || $token[0] == T_INTERFACE) 
         {
            do
            {
               $token = $tokens[++$n];
            }
            while ($token[0] != T_STRING);
            $tmp[] = '\\' . $namespace . $token[1];
         }
      }
      return $tmp;
   }
   
   /**
    * Returns the next token of token array.
    * 
    * @param array $tokens;
    * @param integer $pos
    * @return array|boolean returns FALSE if the current token is the last.
    * @access private
    * @static                         
    */       
   private static function getNextToken(array $tokens, &$pos)
   {
      for ($i = $pos; $i < count($tokens); $i++)
      {
         $token = $tokens[$i];
         if (in_array($token[0], array(371, 368, 370))) continue;
         $pos = $i + 1;
         return $token;
      }
      return false;    
   }
   
   /**
    * Checks the equality of two tokens.
    * 
    * @param string|array $token1
    * @param string|array $token2
    * @return boolean
    * @access private
    * @static                        
    */       
   private static function isEqual($token1, $token2)
   {
      return (is_array($token1) && is_array($token2) && $token1[1] == $token2[1] || $token1 == $token2);  
   }  
}

?>
