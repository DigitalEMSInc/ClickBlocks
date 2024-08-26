# ClickBlocks\Cache\Redis #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Cache\Cache |
| **Child classes** | no |
| **Interfaces** | Countable |
| **Source** | Framework/cache/redis.php |

This class allows to cache any data through the direct connection to the Redis server. The connection is established via sockets. For more information about Redis see [here](http://redis.io/documentation).

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

### **execute()**

```php
public mixed execute(string $command, array $params = [])
```

|||
|-|-|-|
| **$command** | string | the redis command. |
| **$params** | array | the array of the command parameters. |

Executes the given redis command and returns its execution result.

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