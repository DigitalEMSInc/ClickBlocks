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
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 */

namespace ClickBlocks\Cache;

/**
 * The class is intended for caching of different data using the PHP Redis extension.
 *
 * @version 1.0.3
 * @package cb.cache
 */
class CacheRedis implements ICache
{
  /**
   * The instance of \Redis class.
   *
   * @var \Redis $redis
   */
  private $redis;

  /**
   * Constructor.
   *
   * @param string $host - host or path to a unix domain socket for a redis connection.
   * @param integer $port - port for a connection, optional.
   * @param integer $timeout - the connection timeout, in seconds.
   * @param string $password - password for server authentication, optional.
   * @param integer $database - number of the redis database to use.
   * @access public
   */
  public function __construct($host = '127.0.0.1', $port = 6379, $timeout = 0.0, $password = null, $database = 0)
  {
    $this->redis = new \Redis();
    $this->redis->connect($host, $port, $timeout);
    if ($password !== null) $this->redis->auth($password);
    $this->redis->select($database);
    $this->redis->setOption(\Redis::OPT_SERIALIZER, defined('Redis::SERIALIZER_IGBINARY') ? \Redis::SERIALIZER_IGBINARY : \Redis::SERIALIZER_PHP);
  }

  /**
   * Returns the redis object.
   *
   * @return \Redis
   * @access public
   */
  public function getRedis()
  {
    return $this->redis;
  }

  /**
   * Conserves some data identified by a key into cache.
   *
   * @param string $key - a data key.
   * @param mixed $content - some data.
   * @param integer $expire - cache lifetime (in seconds).
   * @access public
   */
  public function set($key, $content, $expire)
  {
    $expire = abs((int)$expire);
    $this->redis->set($key, $content, $expire);
  }

  /**
   * Returns some data previously conserved in cache.
   *
   * @param string $key - a data key.
   * @return boolean
   * @access public
   */
  public function get($key)
  {
    return $this->redis->get($key);
  }

  /**
   * Removes some data identified by a key from cache.
   *
   * @param string $key - a data key.
   * @access public
   */
  public function delete($key)
  {
    $this->redis->del($key);
  }

  /**
   * Checks whether cache lifetime is expired or not.
   *
   * @param string $key - a data key.
   * @return boolean
   * @access public
   */
  public function isExpired($key)
  {
    return !$this->redis->exists($key);
  }

  /**
   * Checks whether the current type of cache is available or not.
   *
   * @return boolean
   * @access public
   * @static
   */
  public static function isAvailable()
  {
    return extension_loaded('redis');
  }

  /**
   * Removes all previously conserved data from cache.
   *
   * @access public
   */
  public function clean()
  {
    $this->redis->flushDB();
  }
  
  /**
   * Closes the current connection with redis.
   *
   * @access public
   */
  public function __destruct()
  {
    $this->redis->close();
  }
}