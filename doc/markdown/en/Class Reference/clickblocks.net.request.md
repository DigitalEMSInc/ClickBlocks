# ClickBlocks\Net\Request #

## General information ##

||
|-|-|
| **Inheritance** | no |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/net/request.php |

Class provides access to HTTP-headers of the current HTTP-request.


## Public static methods ##

### **getInstance()**

```php
public static ClickBlocks\Net\Request getInstance()
```

Returns an instance of the class.

## Public non-static properties ##

### **method**

```php
public string $method
```

Method of the current HTTP-request.

### **headers**

```php
public ClickBlocks\Net\Headers $headers
```

Object of class `ClickBlocks\Net\Headers` that provides access to headers of the current HTTP-request.

### **data**

```php
public array $data = []
```

By default, contains content from **$_GET**, **$_POST** and **$_COOKIE**.

### **files**

```php
public array $files = []
```

By default, contains content from **$_FILES**.

### **server**

```php
public array $server = []
```

By default, contains content from **$_SERVER**.

### **isAjax**

```php
public boolean $isAjax
```

Contains TRUE for all AJAX requests and FALSE otherwise.

### **isMobileBrowser**

```php
public boolean $isMobileBrowser
```

Contains TRUE for all requests that come from mobile browsers and FALSE otherwise.

### **ip**

```php
public string $ip
```

IP address of the client.

### **url**

```php
public ClickBlocks\Net\URL $url
```

Object of class `ClickBlocks\Net\URL` provides access to URL components of the current HTTP-request.

### **body**

```php
public string $body
```

The raw body of the current request. This property is overloaded by using the magic methods **__get()**, **__set()**, **__isset()** and **__unset()**.


## Public non-static methods ##

### **reset()**

```php
public void reset()
```

Initializes all properties of the class with values from PHP's super globals.

### **__toString()**

```php
public string __toString()
```

Returns the request as a string. This method allows to use the class object as a string.


## Protected non-static properties ##

### **body**

```php
protected string $body = null
```

The raw body of the current request.