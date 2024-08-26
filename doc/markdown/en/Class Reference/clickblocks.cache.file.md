# ClickBlocks\Cache\File #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Cache\Cache |
| **Child classes** | no |
| **Interfaces** | ArrayAccess, Countable |
| **Source** | Framework/cache/file.php |

Class for caching data based on the file system. The cached data is serialized and placed in a separate file, which is located in a specified folder (directory cache). Because the file system does not automatically delete expired cache files, the class has a method **gc()**, allowing brush away the cache of non-valid data.

## Public static methods ##

### **isAvailable()**

```php
public static boolean isAvailable()
```

Returns always TRUE because of permanent availability of file system.

## Public non-static methods ##

### **getDirectory()**

```php
public string getDirectory()
```

Returns the current cache directory.

### **setDirectory()**

```php
public void setDirectory(string $path = null)
```

|||
|-|-|-|
| **$path** | string | cache directory. |

Sets the directory to store cache files. If this directory does not exist, then the framework will attempt to create it.

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

### **get()**

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

### **gc()**

```php
public void gc(float $probability = 100)
```

|||
|-|-|-|
| **$probability** | float | probability of method call. |

Garabage collector. Removes all expired cache data and normalizes the vault of group keys.