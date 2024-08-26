# ClickBlocks\Data\Converters\Converter #

## General information ##

||
|-|-|
| **Inheritance** | no |
| **Child classes** | ClickBlocks\Data\Converters\Type, ClickBlocks\Data\Converters\Text, ClickBlocks\Data\Converters\Collection. |
| **Interfaces** | no |
| **Source** | Framework/data/converters/converter.php |

The Converter class is the base abstract class for all classes of converters. If you want to create your own converter class you need to inherit it from the base class and define the method **converter**.

## Public static methods ##

### **getInstance()**

```php
final public static ClickBlocks\Data\Converters\Converter getInstance(string $type, array $params = [])
```

|||
|-|-|-|
| **$type** | string | the converter type name. |
| **$params** | array | the values of the converter properties. |

Creates and returns a converter object of the required type. Converter type can be one of the following values: "type", "text", "collection".

## Public non-static methods ##

### **convert()**

```php
abstract public mixed convert(mixed $entity)
```

|||
|-|-|-|
| **$entity** | mixed | some value to be converted. |

Converts the entity from one data format to another according to the specified options.