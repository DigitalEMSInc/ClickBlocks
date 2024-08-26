# ClickBlocks\Data\Validators\Regex #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/regex.php |

This validator validates that the given value matches to the specified regular expression pattern (PCRE). Example:
```php
// Creates Regex validator.
$validator = new Validators\Regex();
// Sets regular expression: only characters fron "a" to "z" are valid.
$validator->pattern = '/^[a-z]*$';
// Inverts the regular expression: only strings 
// that not containing characters from "a" to "z" considered valid.
$validator->inversion = true;
// Verifies the given values.
echo (int)$validator->validate('abcde'); // output is 0.
echo (int)$validator->validate('12345'); // output is 1.
```

## Public non-static properties ##

### **pattern**

```php
public string $pattern
```

The regular expression to be matched with.

### **inversion**

```php
public boolean $inversion = false
```

Validates a value. The method returns TRUE if the given value matches the specified regular expression. Otherwise, the method returns FALSE.

## Public non-static methods ##

### **validate()**

```php
public boolean validate(string $entity)
```

|||
|-|-|-|
| **$entity** | string | any string to validate. |

Validates a value. The method returns TRUE if the given value matches the specified regular expression. Otherwise, the method returns FALSE.