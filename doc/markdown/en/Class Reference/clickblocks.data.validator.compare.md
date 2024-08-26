# ClickBlocks\Data\Validators\Compare #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/compare.php |

The Compare validator compares the specified value with another value(s) according to the given mode and comparison operator. Example:
```php
// Creates Compare validator.
$validator = new Validators\Compare();
// We want to comparison with multiple values (an array of values).
$validator->hasMultipleValues = true;
// Sets an array of values for comparison.
$validator->value = [3, 11, 17];
// Sets the comparison mode.
$validator->mode = 'or';
// Sets comparison operator.
$validator->operator = '<=';
// Validates the given values.
echo (int)$validator->validate(9); // output is 1.
echo (int)$validator->validate(20); // output is 0.
```

## Public non-static properties ##

### **value**

```php
public mixed $value
```

The value or values to be compared with. If you can compare with multiple values this property should be an array and the property **$hasMultipleValues** should equal TRUE.

### **hasMultipleValues**

```php
public boolean $hasMultipleValues = false
```

Determines whether property **$value** is an array of values or a single value for comparison.

### **mode**

```php
public string $mode = 'and'
```

Determines the way of the calculation of the comparison result with multiple values. If mode equals "and" then the result of the comparison with all values should be TRUE. If mode is "or" then at least one comparison result should be TRUE. If mode equals "xor" then the only one comparison result should get TRUE.

### **operator**

```php
public string $operator = '=='
```

The operator for comparison. The following operators are valid: "==", "!=", "===", "!==", "\<=", "\>=", "\<", "\>".

## Public non-static methods ##

### **validate()**

```php
public boolean validate(mixed $entity)
```

|||
|-|-|-|
| **$entity** | mixed | the value for validation. |

Validates a value. Returns TRUE if the validating value matches the validation conditions and FALSE otherwise.