# ClickBlocks\Net\URL

## General information

||
|-----------------|----|
| **Inheritance** | no |
| **Subclasses**  | no |
| **Interfaces**  | no |
| **Source**      | Framework/net/url.php |

Class provides abilities to modify URL strings, get different URL components and also build arbitrary URL-strings.

## Public static methods

```php
public static string current()
```

Returns URL string of the current HTTP-request. If the script is running in console the method returns FALSE.

## Public non-static properties

### **scheme**

```php
public string $scheme
```

URL scheme.

### **source**

```php
public array $source = []
```

Four-element array: `['host' => ..., 'port' => ..., 'user' => ..., 'pass' => ...]`. Here, **host** is the name of URL host, **port** is the port number, **user** is the username, **pass** is the user password.

### **path**

```php
public array $path = []
```

Array of path components of a URL. For example, URL path `/one/two/three` will be splitted into `['one', 'two', 'three']`. For path `/` (empty path) the property value is empty array.

### **query**

```php
public array $query = []
```

Array of URL query components. For example, for query `?var1=val1&var2=val2&var3=val3` the property value is
`['var1' => 'val1', 'var2' => 'val2', 'var3' => 'val3']`.

### **fragment**

```php
public string $fragment
```

URL fragment.

## Public non-static methods

### **_constructor()**

```php
public void __construct(string $url = null)
```

|||
|----------|--------|------------------------------------------------------------|
| **$url** | string | URL string, that will be splitted to different components. |

Class constructor. The current request URL will be used if **$url** is undefined.

### **parse()**

```php
public string parse(string $url)
```

|||
|----------|--------|------------------------------------------------------------|
| **$url** | string | URL string, that will be splitted to different components. |

Splits URL string to different parts. In the future you can access to components of this URL through appropriate properties of the class.

### **build()**

```php
public string build(integer $component = ClickBlocks\Net\URL::ALL)
```

|||
|----------------|---------|---------------------------------------------------------------------------------|
| **$component** | integer | the constant determines the required component (or set of components) of the URL string. |

Builds the certain part of the URL string. The certain part of URL is specified by using one of the six appropriate class constants. These constants can be combined via bit operations. Example:

```php
$url = new URL('http://user:pass@my.host.com/some/path?p1=v1&p2=v2#frag');
echo $url->build(URL::ALL);                   // displays the whole URL
echo $url->build(URL::SCHEME);                // displays http://
echo $url->build(URL::HOST);                  // displays user:pass@my.host.com
echo $url->build(URL::PATH);                  // displays /some/path
echo $url->build(URL::QUERY);                 // displays p1=v1&p2=v2
echo $url->build(URL::PATH | URL::QUERY);     // displays /some/path?p1=v1&p2=v2
echo $url->build(URL::QUERY | URL::FRAGMENT); // displays p1=v1&p2=v2#frag
```

### **isSecured()**

```php
public boolean isSecured()
```

Returns TRUE, if https protocol is used and FALSE otherwise.

### **secure()**

```php
public void secure(boolean $flag = true)
```

|||
|-----------|---------|--------------------------------------------------|
| **$flag** | boolean | a sign of usage of the protected protocol https. |

Sets the protected HTTP-protocol (https), if the method parameter is TRUE, or unprotected HTTP-protocol (http), if the method parameter is FALSE.

### **__toString()**

```php
public string __toString()
```

Builds and returns the whole URL string. The methods is automatically called when a class object converts to string.


