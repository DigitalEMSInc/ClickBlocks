# ClickBlocks\Cache\Memory #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Cache\Cache |
| **Child classes** | no |
| **Interfaces** | Countable |
| **Source** | Framework/cache/memory.php |

This class is intended for data caching and uses Memcache PHP-extension to cache data in memory (see more about this extension [http://www.php.net/manual/en/book.memcache.php](http://www.php.net/manual/en/book.memcache.php)).

## Public static methods ##

### **isAvailable()**

```php
public static boolean isAvailable()
```

Returns TRUE, if the memcache extension is installed and ready for use, and FALSE otherwise.

## Public non-static methods ## 

### **__construct()**

```php
public void __construct(array $servers, boolean $compress = true)
```

|||
|-|-|-|
| **$servers** | array | an array of configuration parameters for the memcache servers. To get more information see [http://php.net/manual/ru/memcache.addserver.php](http://php.net/manual/ru/memcache.addserver.php) |
| **$compress** | boolean | determines whether the data is compressed before being placed in the cache. The default value is TRUE. |

The class constructor. Allows you to set servers of connection to memcache, and enable data compression.

### **getMemcache()**

```php
public Memcache getMemcache()
```

Returns an object of class **Memcache** for low-level operations with cache.

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
| **$key** | unique identifier of caching data. |

Returns TRUE, if the lifetime of the cache has expired, and FALSE otherwise.

### **clean()**

```php
public void clean()
```

Removes all data from cache.