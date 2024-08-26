# ClickBlocks\Data\Validators\Number #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/number.php |

This validator validates that the given numeric value is in the specified limits or/and has the specific format. Example:
```php
// Creates Number validator.
$validator = new Validators\Number();
// Sets the minimum value.
$validator->min = -10;
// Sets the maximum value.
$validator->max = 10;
// We want to use strict comparison.
$validator->strict = true;
// We expect that the validating value is an integer.
$validator->format = '/^[+-]?[0-9]+$/';
// Verifies the given values.
echo (int)$validator->validate(0); // output is 1.
echo (int)$validator->validate(-10); // output is 0.
echo (int)$validator->validate(2.5); // output is 0.
```

## Public non-static properties ##

### **max**

```php
public float $max
```

The maximum value of the validating number. Null value means no maximum value.

### **min**

```php
public float $min
```

The minimum value of the validating number. Null value means no minimum value.

### **strict**

```
public boolean $strict = false
```

Determines whether the strict comparison is used.

### **format**

```php
public string $format
```

Determines valid format (PCRE) of the given number. Null value means no format checking.

## Public non-static methods ##

### **validate()**

```php
public boolean validate(mixed $entity)
```

|||
|-|-|-|
| **$entity** | mixed | the numeric value for validation. |

Validates a number value. If the given value is not number, it will be converted to number. The method returns TRUE if value of the given number is in the required limits or/and has the specific format (determining by the given regular expression). Otherwise, the method returns FALSE.