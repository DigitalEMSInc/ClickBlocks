<?php

namespace ClickBlocks\Cache;

use Illuminate\Contracts\Cache\Repository;

/**
 * The class is intended for caching of different data using the PHP Redis extension.
 */
class CacheLaravel implements ICache
{
    /**
     * Cache repository
     *
     * @var Repository
     */
    private $cacheRepository;
    
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
    public function __construct()
    {
        $this->cacheRepository = app(Repository::class);
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
        $this->cacheRepository->set($key, $content, $expire);
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
        return $this->cacheRepository->get($key);
    }
    
    /**
     * Removes some data identified by a key from cache.
     *
     * @param string $key - a data key.
     * @access public
     */
    public function delete($key)
    {
        $this->cacheRepository->delete($key);
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
        return !$this->cacheRepository->has($key);
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
        return true;
    }
    
    /**
     * Removes all previously conserved data from cache.
     *
     * @access public
     */
    public function clean()
    {
        $this->cacheRepository->clear();
    }
}