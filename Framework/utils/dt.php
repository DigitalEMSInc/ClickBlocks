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
 * Responsibility of this file: dt.php
 *
 * @category   MVC
 * @package    MVC
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Utils;

/**
 * A DT intended for the conversion of a date into various formats.
 *
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class DT
{
   /**
    * The instance of a \DateTime class.
    *
    * @var    DateTime
    * @access private
    */
   private $dt = null;

   /**
    * Constructs a new DT
    *
    * - new DT() constructs an instance of DT for the current date in the format 'm/d/Y H:i:s'.
    *
    * - new DT($date) constructs an instance of DT for the date $date in the format 'm/d/Y H:i:s'.
    *
    * - new DT($date, $timezone) constructs of DT for the date $date for the timezone $timezone;
    *
    * @param  string $data
    * @param  string $timezone
    * @access public
    */
   public function __construct($date = 'now', \DateTimeZone $timezone = null)
   {
      $this->dt = ($timezone) ? new \DateTime($date, $timezone) : new \DateTime($date);
   }

   public function modify($format)
   {
      $this->dt->modify($format);
      return $this;
   }

   /**
    * Adds the number of days to a date. The number of days maybe negative.
    *
    * @param  integer $days
    * @return DT
    * @access public
    */
   public function addDays($days)
   {
      $this->dt->modify((($days > 0) ? '+' : '-') . abs((int)$days) . ' day');
      return $this;
   }

   /**
    * Adds the number of months to a date. The number of months maybe negative.
    *
    * @param  integer $months
    * @return DT
    * @access public
    */
   public function addMonths($months)
   {
      $this->dt->modify((($months > 0) ? '+' : '-') . abs((int)$months) . ' month');
      return $this;
   }

   /**
    * Adds the number of years to a date. The number of yers maybe negative.
    *
    * @param  integer $years
    * @return DT
    * @access public
    */
   public function addYears($years)
   {
      $this->dt->modify((($years > 0) ? '+' : '-') . abs((int)$years) . ' year');
      return $this;
   }

   /**
    * Adds the number of hours to a date. The number of hours maybe negative.
    *
    * @param  integer $hours
    * @return DT
    * @access public
    */
   public function addHours($hours)
   {
      $this->dt->modify((($hours > 0) ? '+' : '-') . abs((int)$hours) . ' hour');
      return $this;
   }

   /**
    * Adds the number of minutes to a date. The number of minutes maybe negative.
    *
    * @param  integer $minutes
    * @return DT
    * @access public
    */
   public function addMinutes($minutes)
   {
      $this->dt->modify((($minutes > 0) ? '+' : '-') . abs((int)$minutes) . ' minute');
      return $this;
   }

   /**
    * Adds the number of seconds to a date. The number of seconds maybe negative.
    *
    * @param  integer $seconds
    * @return DT
    * @access public
    */
   public function addSeconds($seconds)
   {
      $this->dt->modify((($seconds > 0) ? '+' : '-') . abs((int)$seconds) . ' second');
      return $this;
   }

   /**
    * Returns a new date in the given format.
    *
    * @param  format
    * @return string
    * @access public
    */
   public function getDate($format = 'm/d/Y H:i:s')
   {
      return $this->dt->format($format);
   }

   /**
    * Returns the value of hour component of the given date.
    *
    * @param  string $date
    * @param  string $format
    * @return integer
    * @access public
    * @static
    */
   public static function getHour($date, $format)
   {
      $info = self::getDateTimeInfo($date, $format);
      return $info['hour'];
   }

   /**
    * Returns the value of minute component of the given date.
    *
    * @param  string $date
    * @param  string $format
    * @return integer
    * @access public
    * @static
    */
   public static function getMinute($date, $format)
   {
      $info = self::getDateTimeInfo($date, $format);
      return $info['minute'];
   }

   /**
    * Returns the value of seconds of the given date.
    *
    * @param  string $date
    * @param  string $format
    * @return integer
    * @access public
    * @static
    */
   public static function getSecond($date, $format)
   {
      $info = self::getDateTimeInfo($date, $format);
      return $info['second'];
   }

   /**
    * Returns the value of days of the given date.
    *
    * @param  string $date
    * @param  string $format
    * @return integer
    * @access public
    * @static
    */
   public static function getDay($date, $format)
   {
      $info = self::getDateTimeInfo($date, $format);
      return $info['day'];
   }

   /**
    * Returns the value of months of the given date.
    *
    * @param  string $date
    * @param  string $format
    * @return integer
    * @access public
    * @static
    */
   public static function getMonth($date, $format)
   {
      $info = self::getDateTimeInfo($date, $format);
      return $info['month'];
   }

   /**
    * Returns the value of years of the given date.
    *
    * @param  string $date
    * @param  string $format
    * @return integer
    * @access public
    * @static
    */
   public static function getYear($date, $format)
   {
      $info = self::getDateTimeInfo($date, $format);
      return $info['year'];
   }

   /**
    * Returns the associative array of the components of the given date in the following format:
    * array('hour' => ..., 'minute' => ..., 'second' => ..., 'day' => ..., 'month' => ..., 'year' => ...);
    *
    * @param  string $date
    * @param  string $format
    * @return array
    * @access public
    * @static
    */
   public static function getDateTimeInfo($date, $format)
   {
      return date_parse_from_format($format, $date);
   }

   /**
    * Возвращает метку времени для даты определённого формата.
    *
    * Returns unix timestamp for the date given in any format.
    *
    * @param  string $date
    * @param  string $format
    * @return integer
    * @access public
    * @static
    */
   public static function timestamp($date, $format)
   {
      $dt = date_create_from_format($format, $date);
      return ($dt) ? date_timestamp_get($dt) : false;
   }

   /**
    * Конвертирует дату из одного формата в другой.
    *
    * Converts the date from one format in another.
    *
    * @param  string $date
    * @param  string $in
    * @param  string $out
    * @return string
    * @access public
    * @static
    */
   public static function format($date, $in, $out)
   {
      $dt = date_create_from_format($in, $date);
      return ($dt) ? date_format($dt, $out) : false;
   }

   /**
    * Возвращает число дней в месяца для заданного года.
    *
    * Returns the number of days at a month.
    *
    * @param  integer $year
    * @param  integer $month
    * @return integer
    * @access public
    * @static
    */
   public static function getDays($year, $month)
   {
      if ($month == 2)
      {
         $days = 28;
         if (($year % 4) == 0 && ($year % 100) != 0 || ($year % 400) == 0) $days++;
      }
      else if ($month < 8) $days = 30 + $month % 2;
      else $days = 31 - $month % 2;
      return $days;
   }

   /**
    * Comparison of the two given dates.
    * if the first date larger than the second date then the method returns 1.
    * if the first date smaller than the second date then the method returns -1.
    * Otherwise the method returns 0.
    *
    * @param  string $date1
    * @param  string $date2
    * @param  string $format
    * @return integer
    * @access public
    * @static
    */
   public static function compare($date1, $date2, $format)
   {
      $v1 = DT::timestamp($date1, $format);
      $v2 = DT::timestamp($date2, $format);
      if ($v1 > $v2) return 1;
      if ($v2 > $v1) return -1;
      return 0;
   }

   /**
    * Computes the difference in the components of a date between two dates given in the same format.
    * The value of component of a date maybe one of following values: second, minute, hour, day, week, month, year.
    *
    * @param  string $date1
    * @param  string $date2
    * @param  string $format
    * @param  string $component
    * @return string
    * @link http://php.net/manual/en/dateinterval.format.php
    */
   public static function difference($date1, $date2, $format, $component = '%R%a')
   {
      $dt1 = date_create_from_format($format, $date1);
      $dt2 = date_create_from_format($format, $date2);
      return ($dt1 && $dt2) ? date_diff($dt1, $dt2)->format($component) : false;
   }

   public static function countdown($date1, $date2, $format)
   {
      $stamp1 = self::timestamp($date1, $format);
      $stamp2 = self::timestamp($date2, $format);
      $d = $stamp2 - $stamp1;
      $res = array();
      $res['day'] = floor($d / 86400);
      $d = $d % 86400;
      $res['hour'] = floor($d / 3600);
      $d = $d % 3600;
      $res['minute'] = floor($d / 60);
      $res['second'] = $d % 60;
      return $res;
   }

   /**
    * Проверяет является ли год високосным.
    *
    * @param  mixed $year
    * @return boolean
    * @static
    */
   public static function isLeapYear($year = null)
   {
      if (is_string($year)) $year = date('Y') + (int)$year;
      return checkdate(2, 29, ($year === null) ? date('Y') : (int)$year);
   }

   /**
    * Validates the specified date format.
    *
    * @param  string $date
    * @param  string $format
    * @return boolean
    * @access public
    * @static
    */
   public static function isDate($date, $format)
   {
      return (date_create_from_format($format, $date) !== false);
   }

   public static function date2sql($date, $format, $shortFormat = false)
   {
      return self::format($date, $format, ($shortFormat) ? 'Y-m-d' : 'Y-m-d H:i:s');
   }

   public static function sql2date($date, $format, $shortFormat = false)
   {
      return self::format($date, ($shortFormat) ? 'Y-m-d' : 'Y-m-d H:i:s', $format);
   }
}

?>
