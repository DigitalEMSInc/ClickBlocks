# ClickBlocks\Data\Converters\Text #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Converters\Converter |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/converters/text.php |

The Text converter is intended for converting the given string (or, in some cases, any value) into another data format specified by the input and output string format. Example:
```php
// Creates Text converter.
$converter = new Converters\Text();
// Sets the input type of the string.
$converter->input = 'json-encoded';
// Sets the output data type.
$converter->output = 'serialized';
// Converts our string to PHP-serialized string.
$json = json_encode(['a', 'b', 'c']);
echo $converter->converts($json);
// output is a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}
```

## Public non-static properties ##

### **input**

```php
public string $input = 'any'
```

The input string format. It can be one of the following valid values: "any", "plain", "serialized", "json-encoded", "base64-encoded", "uu-encoded".

You should use type "any" in all cases when the given value is not a scalar value or you don't know what format the value has.

### **output**

```php
public string $output = 'any'
```

The output data format. It can be one of the following valid values: "any", "plain", "serialized", "unserialized", "json-encoded", "json-decoded", "base64-encoded", "base64-decoded", "uu-encoded", "uu-decoded".

To allow the class to solve what output format could be fitted you should use "any" type.

### **inputCharset**

```php
public string $inputCharset = 'UTF-8'
```

The charset of the input string.

### **outputCharset**

```php
public string $outputCharset = 'UTF-8'
```

The charset of the output data.

## Public non-static methods ##

### **convert()**

```php
public mixed convert(mixed $entity)
```

|||
|-|-|-|
| **$entity** | mixed | any data for conversion. |

Converts the given value to the specified data format according to the given character sets.

## Protected non-static methods ##

### **convertEncoding()**

```php
protected string convertEncoding(string $entity)
```

|||
|-|-|-|
| **$entity** | string | the string value to be converted. |

Converts the character encoding of the given string to **$outputCharset** from **$inputCharset**.