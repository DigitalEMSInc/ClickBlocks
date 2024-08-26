# ClickBlocks\Data\Converters\Type #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Converters\Converter |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/converters/type.php |

The Type converter converts the variable of the given type into a variable of another type. Example:
```php
// Creates Type converter.
$converter = new Converters\Type();
// We will convert to boolean type.
$converter->type = 'bool';
// Converting.
var_dump($converter->convert(1)); // output is "bool(true)"
var_dump($converter->convert(0)); // output is "bool(false)"
var_dump($converter->convert([])); // output is "bool(false)"
var_dump($converter->convert(new \stdClass)); // output is "bool(true)"
```

## Public non-static properties ##

### **type**

```php
public string $type = 'string'
```

The data type into which the given variable should be converted. Valid values include "null", "string", "boolean" (or "bool"), "integer" (or "int"), "float" (or "double" or "real"), "array", "object".

## Public non-static methods ##

### **convert()**

```php
public mixed convert(mixed $entity)
```

|||
|-|-|-|
| **$entity** | mixed | the value for conversion. |

Set the type of the given variable **$entity** to the specified PHP data type. Returns a variable of the specified type or throws exception if such conversion is not possible.
