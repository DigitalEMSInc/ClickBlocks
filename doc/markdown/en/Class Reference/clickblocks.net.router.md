# ClickBlocks\Net\Router #

## General information ##

||
|-------------------|----|
| **Inheritance**   | no |
| **Child classes** | no |
| **Interfaces**    | no |
| **Source**        | Framework/net/router.php |

This class is the router of requests. It allows to perform previously specified actions according to request type.

Every action is bound to one or more methods of the current HTTP-request and must meet an URL of certain kind. Matching with URL is carried out by specifying the appropriate regular expression. This regular expression is superset Perl-compatible regular expressions (PCRE) in which some characters have the special meaning:

- Symbol **#**. It is used for moving of some URL part into a variable. For example, template`category/#category#/#ID#` will match the following URL:
  - category/my_first_category/5
  - category/my_second_category/10
  - category/my_third_category/15
  - ...
  
  In this case the following values will correspond the variable "category": my_first_category, my_second_category, my_third_category and so on. The variable "ID" will take the values: 5, 10, 15 and so on.
  If it is necessary to use symbol **#** without special meaning then you need to escape it by backslash: `category/#category#/#ID#\#fragment`.

-   Symbol **|** is used for setting of regular expression that bound with template variable. For example, if we want to limit the variable "ID" from previous example only integer numbers then we can write such regular expression: `category/#category#/#ID|[0-9].#`. If after symbol **|** we use another one then it will be treated as regular symbol of alternative in regular expressions.

## Public non-static methods ##

### **bind()**

```php
public ClickBlocks\Net\Router bind(string $regex, mixed $action, string|array $methods = '*')
```

|||
|-|-|-|
| **$regex** | string | regular expression (superset of PCRE) that corresponds some URL or its part. |
| **$action** | mixed | delegate or any callable object that is called in case of matching URL to the given regular expression. |
| **$methods** | string, array | array of HTTP-methods for which the matching URL with the given regular expression should be performed. |

Allows to bind a delegate with specific URL for the given HTTP-methods.
Calling the delegate will only take place if the current method of HTTP-request matches one of the given methods, and the regular expression matches to URL of the current request.
Instead of array of HTTP-methods you can pass a string in which method names are separated by symbol "|". Example: GET|POST|HEAD. Also parameter **$methods** can be symbol "\*", that is equivalent to methods: GET, PUT, POST и DELETE.  If method **$methods** equals "@", then it is equivalent to all HTTP-methods: GET, PUT, POST, DELETE, HEAD, OPTIONS, TRACE и CONNECT.

### **redirect()**

```php
public ClickBlocks\Net\Router redirect(string $regex, string $redirect, string|array $methods = '*', mixed $callback = null)
```

|||
|-|-|-|
| **$regex** | string | regular expression (superset of PCRE), matching some URL or its part. |
| **$redirect** | string | URL to redirect. |
| **$methods** | string, array | array of HTTP-methods for which the matching URL with the given regular expression should be performed. |
| **$callback** | mixed | delegate that will be invoked instead of redirect. |

Performs the redirect to the specified address.
URL for redirect can contains variables of template **$regex** which will be replaced by their values. For example, if for the URL template `category/#category#/#ID#` we set redirect URL `post/#ID#/#category#`, then process of the request with URL `category/foo/111` will lead to redirect to URL `post/111/foo`.
Redirect does not happen if the last parameter of the method is a delegate. Instead of redirect this delegate is called and it will take a fully formed URL to redirect to.

### **secure()**

```php
public ClickBlocks\Net\Router secure(string $regex, boolean $flag, string|array $methods = '*', mixed $callback = null)
```

|||
|-|-|-|
| **$regex** | string | regular expression (PCRE), matching some URL or its part. |
| **$flag** | boolean | enables or disables the use of a secure protocol. |
| **$methods** | string, array | array of HTTP-methods for which the matching URL with the given regular expression should be performed. |
| **$callback** | mixed | delegate that will be invoked instead of redirect. |

Enables or disables the use of the HTTPS protocol for a given template URL. If the URL of the current request matches a given pattern (parameter **$regex**) and the parameter **$flag** is TRUE, it will redirect to the same URL, but already with the protocol HTTPS. If **$flag** is FALSE, then the redirect will change the protocol to HTTP.
The last argument of the method defines a delegate that will be called instead of the redirect. The delegate takes URL to redirect as a parameter.

### **remove()**

```php
public ClickBlocks\Net\Router remove(string $regex, string $type = null, string|array $methods = '*') 
```

|||
|-|-|-|
| **$regex** | string | regular expression, matching some URL or its part. |
| **$type** | string | defines type of URL templates, can be one of three following values: bind, redirect или secure. |
| **$methods** | string, array | array of HTTP-methods for which the matching URL with the given regular expression should be performed. |

Removes the delegate that associated with some URL-template for the given HTTP-method.
If type of URL templates is not defined (parameter **$type**) then the delegate will be removed for all three types of templates.

### **clean()**

```php
public ClickBlocks\Net\Router clean(string $type = null)
```

|||
|-|-|-|
| **$type** | string | defines type of URL templates, can be one of three following values: bind, redirect или secure. |

Removes all delegates for one or all types of URL templates.

### **component()**

```php
public ClickBlocks\Net\Router component(integer $component)
```

|||
|-|-|-|
| **$component** | integer | a constant or constant combination of class `ClickBlocks\Net\URL`, determining URL component of the current HTTP-request for which the regular expression, that specified in method **bind()**, **redirect()** или **secure()**, will be applied. |

Allows to specify which part of the request URL should be used for matching with the regular expressions of URL-templates.

### **validation()**

```php
public ClickBlocks\Net\Router validation(array $validation)
```

|||
|-|-|-|
| **$validation** | array | array of regular expressions for variables of URL-template. |

The method allows you to specify to check for variables is the regular expression. The parameter of the method is an associative array whose keys are the names of template variables and values ​​- regular expressions to validate the values ​​of appropriate variables.
If at least one variable does not coincide with your regular expression, then the appropriate action of the router will fail.

### **args()**

```php
public ClickBlocks\Net\Router args(array $args)
```

|||
|-|-|-|
| **$args** | array | array of parameters that will be passed to the delegate. |

Specifies an array of parameters to be passed to the appropriate delegate. If the URL-template includes the variables, the delegate will take in the first place parameter **$args** and then only parameters that related to template variables.

### **extra()**

```php
public ClickBlocks\Net\Router extra(string $parameter = 'extra')
```

|||
|-|-|-|
| **$parameter** | string | name of delegate parameter that will take an array of values of template variables. |

Specifies the name of the delegate argument to pass to it the values ​​of template variables.

### **coordinateParameterNames()**

```php
public ClickBlocks\Net\Router coordinateParameterNames(boolean $flag = true)
```

|||
|-|-|-|
| **$flag** | boolean | determines whether to synchronize the names of variables and parameters of the delegate URL-template. |

Enables or disables the synchronization of variable names and URL-template parameter names corresponding to the delegate. By default, synchronization is disabled. If synchronization is enabled, the delegate will only receive template variables whose names match the names of the parameters of the delegate.

### **ignoreWrongDelegate()**

```php
public ClickBlocks\Net\Router ignoreWrongDelegate(boolean $flag = true)
```

|||
|-|-|-|
| **$flag** | boolean | determines whether to verify the correctness of the delegate. |

If the method parameter is TRUE, then the delegate who may be called to be missed and the router will continue to work. If the parameter is FALSE, then when you try to access an invalid delegate will be thrown. By default, the delegates are not capable of ignoring the challenges of the disabled.

### **ssl()**

```php
public ClickBlocks\Net\Router ssl(boolean $flag)
```

|||
|-|-|-|
| **$flag** | boolean | if equals TRUE, the request is supposed to be secured. |

Determines whether the request should have the secured HTTPS protocol.

### **route()**

```php
public array route(string|array $methods = null, string|ClickBlocks\Net\URL $url = null)
```

|||
|-|-|-|
| **$methods** | string, array | HTTP-method or methods for which URL-templates should be analyzed. If the parameter is not set then method of the current HTTP-request will be used. |
| **$url** | string, ClickBlocks\Net\URL | URL for template matching. If not specified, it will be taken URL of the current request. |

Checks the request URL mapping to a certain URL pattern for the specified HTTP-method. If a match is found, it will be invoked delegate, previously tied to the pattern.
Method Returns a array of the following structures: `['success' => ... [boolean] ..., 'result' => ... [mixed] ...]`
The first element of the array contains TRUE, if the pattern match is found and FALSE otherwise. The second element contains the result of calling the delegate.
