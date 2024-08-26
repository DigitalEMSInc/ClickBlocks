# ClickBlocks\Cache\Session #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Cache\Cache |
| **Child classes** | no |
| **Interfaces** | Countable |
| **Source** | Framework/cache/session.php |

Class is intended for caching any data in PHP session. This is fake cache system, but it can be used for testing some functionality of caching in your application.

> All cached data are accessed from `$_SESSION['__CACHE__']`

## Public static methods ##

### **isAvailable()**

```php
public static boolean isAvailable()
```

Returns TRUE if PHP session is statrted and ready to work and FALSE otherwise.


## Public non-static methods ##

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

Stores data in cache by their unique identifier.

### **get()**

```php
public mixed get(string $key)
```

|||
|-|-|-|
| **$key** | string | unique identifier of caching data. |

Returns previously stored in cache data by their identifier.

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

Returns TRUE, if the lifetime of the cache has expired, and FALSE otherwise.

### **clean()**

```php
public void clean()
```

Removes all data from cache.

### **gc()**

```php
public void gc(float $probability = 100)
```

|||
|-|-|-|
| **$probability** | float | probability of method call. |

Garabage collector. Removes all expired cache data and normalizes the vault of group keys.