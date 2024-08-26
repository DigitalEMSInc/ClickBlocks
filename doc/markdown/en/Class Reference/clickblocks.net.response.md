# ClickBlocks\Net\Response #

## General information ##

||
|-|-|
| **Inheritance** | no |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/net/response.php |

Class provides access to headers of the last HTTP-response of the server and allows to form the current server HTTP-response.

## Public static methods ##

### **getInstance()**

```php
public static ClickBlocks\Net\Response getInstance()
```

Returns an instance of the class.

### **getMessage()**

```php
public static string getMessage(integer $status)
```

|||
|-|-|-|
| **$status** | integer | status of the HTTP-response. |

Returns status message of the server response by its status code. If such status code does not exist the method returns FALSE.

## Public non-static properties ##

### **headers**

```php
public ClickBlocks\Net\Headers $headers
```

Object of class ClickBlocks\Net\Headers that provides access to headers of the server HTTP-response.

### **version**

```php
public string $version = '1.1'
```

Version of the HTTP-protocol.

### **status**

```php
public integer $status = 200
```

Status of the HTTP-response.

### **body**

```php
public string $body
```

Body of the HTTP-response.

## Public non-static methods ##

### **markAsPrivate()**

```php
public void markAsPrivate()
```

Marks the response as "private". It makes the response ineligible for serving other clients.

### **markAsPublic()**

```php
public void markAsPublic()
```

Marks the response as "public". It makes the response eligible for serving other clients.

### **markAsNotModified()**

```php
public void markAsNotModified()
```

Modifies the response so that it conforms to the rules defined for a 304 status code. This sets the status, removes the body, and discards any headers that MUST NOT be included in 304 responses.

### **getContentType()**

```php
public string|array getContentType(boolean $withCharset = false)
```

|||
|-|-|-|
| **$withCharset** | boolean | if TRUE the method returns an array of the following structure `['type' => ..., 'charset' => ...]`, otherwise only content type will be returned. |

Returns the content type and/or response charset.

### **setContentType()**

```php
public void setContentType(string $type, string $charset = null)
```

|||
|-|-|-|
| **$type** | string | the response content type. |
| **$charset** | string | the response charset. |

Sets content type header. You can use content type alias instead of some HTTP headers.

### **getCharset()**

```php
public string getCharset()
```

Returns the response charset. If the "Content-Type" header with charset is not set the method returns NULL.

### **setCharset()**

```php
public void setCharset(string $charset = 'UTF-8')
```

|||
|-|-|-|
| **$charset** | string | the response charset. |

Sets the response charset.

### **getDate()**

```php
public DateTime getDate()
```

Returns the "Date" header of the response as a DateTime instance. If the "Date" header is not set or not parseable the method returns a DateTime object that represents the current time.

### **setDate()**

```php
public void setDate(DateTime|string $date = 'now')
```

|||
|-|-|-|
| **$date** | string, DateTime | a DateTime instance or date string. |

Sets the "Date" header value.

### **getExpires()**

```php
public DateTime getExpires()
```

Returns the value of the "Expires" header as a DateTime instance.

### **setExpires()**

```php
public void setExpires(string|DateTime $date = null)
```

|||
|-|-|-|
| **$date** | string, DateTime | a DateTime instance, date string or NULL to remove the header. |

Sets the "Expires" header value. Passing NULL as value will remove the header.

### **getLastModified()**

```php
public DateTime getLastModified()
```

Returns the Last-Modified HTTP header as a DateTime instance. The method returns FALSE if the header does not exist.

### **setLastModified()**

```php
public void setLastModified(string|DateTime $date = null)
```

|||
|-|-|-|
| **$date** | string, DateTime | a DateTime instance, date string or NULL to remove the header. |

Sets the "Last-Modified" HTTP header with a DateTime instance. Passing NULL as value will remove the header.

### **getAge()**

```php
public integer getAge()
```

Returns amount of time (in seconds) since the response (or its revalidation) was generated at the origin server.

### **getMaxAge()**

```php
public integer getMaxAge()
```

Returns the number of seconds after the time specified in the response's "Date" header when the response should no longer be considered fresh.

### **setMaxAge()**

```php
public void setMaxAge(integer $value)
```

|||
|-|-|-|
| **$value** | integer | the number of seconds. |

Sets the number of seconds after which the response should no longer be considered fresh. This methods sets the cache control "max-age" directive.

### **setSharedMaxAge()**

```php
public void setSharedMaxAge(integer $value)
```

|||
|-|-|-|
| **$value** | integer | the number of seconds. |

Sets the number of seconds after which the response should no longer be considered fresh by shared caches. This methods sets the cache control "s-maxage" directive.

### **getTTL()**

```php
public integer getTTL()
```

Returns the response's time-to-live in seconds.

### **setTTL()**

```php
public void setTTL(integer $value)
```

|||
|-|-|-|
| **$value** | integer | the number of seconds. |

Sets the response's time-to-live for shared caches. This method adjusts the cache control "s-maxage" directive.

### **setClientTTL()**

```php
public void setClientTTL(integer $value)
```

|||
|-|-|-|
| **$value** | integer | the number of seconds. |

Sets the response's time-to-live for private client caches. This method adjusts the cache control "max-age" directive.

### **getEtag()**

```php
public string getEtag()
```

Returns the literal value of the "ETag" HTTP header.

### **setEtag()**

```php
public setEtag(string $etag = null, boolean $weak = false)
```

|||
|-|-|-|
| **$etag** | string | the ETag unique identifier or NULL to remove the header. |
| **$weak** | boolean | determines whether you want a weak ETag or not. |

Sets the "ETag" header value.

### **reset()**

```php
public void reset()
```

Initializes all properties of the class with values from PHP's super globals.

### **clean()**

```php
public void clean()
```

Removes all HTTP-headers.

### **cache()**

```php
public void cache(integer|boolean $expires)
```

|||
|-|-|-|
| **$expires** | integer, boolean | response cache lifetime in seconds or FALSE if it is necessary to turn off the response caching. |

Caches the server response for some time.

### **redirect()**

```php
public void redirect(string $url, integer $status = 302)
```

|||
|-|-|-|
| **$url** | string | URL to redirect. |
| **$status** | integer | status of the server response. |

Redirects immediately to the specified URL. After calling this method, the script terminates.

### **stop()**

```php
public void stop(integer $status = 500, string $message = null)
```

|||
|-|-|-|
| **$status** | integer | status of the server response. |
| **$message** | string | content of the response body. |

Terminates the script execution and sets the status code and message of the server response.

### **download()**

```php
public void download(string $path, string $filename = null, string $contentType = null, boolean $deleteAfterDownload = false)
```

|||
|-|-|-|
| **$path** | string | the full path to the file. |
| **$filename** | string | the name of the downloading file. |
| **$contentType** | string | the content type of the downloading file. |
| **$deleteAfterDownload** | boolean | determines whether the file should be deleted after download. |

Downloads the given file. If the content type is not defined, it will be determined according to content of the file.

### **send()**

```php
public void send()
```

Sends HTTP-headers of the server response. Script execution is not terminated as a result of calling of this method.


## Protected static properties ##

### **codes**

```php
protected static array $codes = [...]
```

Array of message which matches the codes of the server response.