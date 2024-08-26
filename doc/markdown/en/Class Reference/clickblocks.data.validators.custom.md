# ClickBlocks\Data\Validators\Custom #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/custom.php |

The Custom validator allows to validate any values according to the user defined rules via setting of the user callback function. Example:
```php
// Creates Custom validator.
$validator = new Validators\Custom();
// Sets the callback function.
$validator->callback = function($entity, $validator)
{
  return (float)$entity < 10 && is_string($entity);
};
// Validates the given values.
echo (int)$validator->validate('5.5'); // output is 1.
echo (int)$validator->validate(5.5); // output is 0.
```

## Public non-static properties ##

### **callback**

```php
public mixed $callback
```

The callback that will be automatically invoked as the custom validation method. This callback takes a value for validation as the first parameter and the validator object as the second one.

## Public non-static methods ##

### **validate()**

```php
public boolean validate(mixed $entity)
```

|||
|-|-|-|
| **$entity** | mixed | any value for validation. |

Validates a value via the custom validation method. The custom method takes the validating value as the first parameter and the validator object as the second one. Moreover it should return boolean value as a result of the validation.