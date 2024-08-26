# ClickBlocks\Data\Validators\Required #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/required.php |

You can use this validator to check whether the given value is empty or not. Example:
```php
// Creates Required validator.
$validator = new Validators\Required();
// Validates the given values.
echo (int)$validator->validate(null); // output is 0.
echo (int)$validator->validate(''); // output is 0.
echo (int)$validator->validate([]); // output is 0.
echo (int)$validator->validate(new \stdClass); // output is 0.
echo (int)$validator->validate(0); // output is 1.
echo (int)$validator->validate('test'); // output is 1.
echo (int)$validator->validate([1, 2, 3]); // output is 1.
echo (int)$validator->validate($validator); // output is 1.
```

## Public non-static method ##

### **validate()**

```php
public boolean validate(mixed $entity)
```

|||
|-|-|-|
| **$entity** | mixed | any value for validation. |

Validates a value. For scalar values the method returns TRUE if the given value is not empty value (not an empty string) and FALSE otherwise. If the given value is an array the methods return TRUE if this array has at least one element and FALSE otherwise. If the given value is an object the methods return TRUE if this object has at least one public property and FALSE otherwise.