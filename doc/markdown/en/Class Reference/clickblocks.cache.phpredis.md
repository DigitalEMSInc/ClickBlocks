# ClickBlocks\Cache\PHPRedis #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Cache\Cache |
| **Child classes** | no |
| **Interfaces** | Countable |
| **Source** | Framework/cache/phpredis.php |

This class is intended for caching any data using by Redis PHP extension. See more information [here](https://github.com/nicolasff/phpredis)

## Public static methods ## 

### **isAvailable()**

```php
public static boolean isAvailable()
```

Returns TRUE if the Redis extension is enabled on your system and FALSE otherwise.

## Public non-static methods ##

### **__construct()**

```php
public void __construct(string $host = '127.0.0.1', integer $port = 6379, integer $timeout = 0, string $password = null, integer $database = 0)
```

|||
|-|-|-|
| **$host** | string | host or path to a unix domain socket for a redis connection. |
| **$port** | integer | port for a connection, optional. |
| **$timeout** | integer | the connection timeout, in seconds. |
| **$password** | string | password for server authentication, optional. |
| **$database** | integer | number of the redis database to use. |

Constructor. Establishes connection with the redis server.

### **getRedis()**

```php
public Redis getRedis()
```

Returns an instance of the **Redis** class for performing low-level operations.

### **set()**

```php
public void set(string $key, mixed $content, integer $expire, string $group = null)
```

|||
|-|-|-|
| **$key** | string | unique identifier of caching data. |
| **$content** | mixed | any data to cache. |
| **$expire** | integer | cache lifetime in seconds. |
| **$group** | string | cache group name. |

Stores data in cache by their identifier.

### **get()**

```php
public mixed get(string $key)
```

|||
|-|-|-|
| **$key** | string | unique identifier of caching data. |

Returns previously stored data from cache by their identifier.

### **remove()**

```php
public void remove(string $key)
```

|||
|-|-|-|
| **$key** | string | unique identifier of caching data. |

Removes data from cache by their identifier.

### **isExpired()**

```php
public boolean isExpired(string $key)
```

|||
|-|-|-|
| **$key** | string | unique identifier of caching data. |

Returns TRUE, if the lifetime of the cache has expired, and FALSE otherwise. If the lifetime of the cache expires, the cache file will be deleted.

### **clean()**

```php
public void clean()
```

Removes all data from cache.