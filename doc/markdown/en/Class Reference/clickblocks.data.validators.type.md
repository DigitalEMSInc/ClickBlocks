# ClickBlocks\Data\Validators\Type #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/type.php |

The Type validator verifies if the given value is of the specified data type or not. Example:
```php
// Creates Type validator.
$validator = new Validators\Type();
// We need to check for "integer" data type.
$validator->type = 'integer';
// Verifies value.
echo (int)$validator->validate(1234); // output is 1
echo (int)$validator->validate(12.34); // output is 0
```

## Public non-static properties ##

### **type**

```php
public string $type = 'string'
```

The PHP data type that the validating value should be.  Valid values include "null", "string", "boolean" (or "bool"), "integer" (or "int"), "float" (or "double" or "real"), "array", "object", "resource".

## Public non-static methods ##

### **validate()**

```php
public boolean validate(mixed $entity)
```

|||
|-|-|-|
| **$entity** | mixed | any value for validating. |

Validates a value. The method returns TRUE if the given value has the required data type. Otherwise, the method returns FALSE.