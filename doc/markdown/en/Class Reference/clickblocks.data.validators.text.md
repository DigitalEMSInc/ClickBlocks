# ClickBlocks\Data\Validators\Text #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/text.php |

The Text validator checks that the given string value has the length in the specific limits. Example:
```php
// Creates Text validator.
$validator = new Validators\Text();
// Sets lower and upper limits of the string length.
$validator->min = 3;
$validator->max = 6;
// Validates the given values.
echo (int)$validator->validate('1234'); // output is 1;
echo (int)$validator->validate('12'); // output is 0;
```

## Public non-static properties ##

### **max**

```php
public integer $max 
```

The maximum length of the validating string. Null value means no maximum limit.

### **min**

```php
public integer $min
```

The minimum length of the validating string. Null value means no minimum limit.

### **charset**

```php
public string $charset = false
```

The encoding of the string value to be validated. This property is used only when mbstring PHP extension is enabled. The value of this property will be used as the 2nd parameter of the mb_strlen() function. If this property is not set, the internal character encoding value will be used (see function mb_internal_encoding()). If this property is FALSE, then strlen() will be used even if mbstring is enabled.

## Public non-static methods ##

### **validate()**

```php
public boolean validate(string $entity)
```

|||
|-|-|-|
| **$entity** | string | any string for validation. |

Validates a string value. If the given value is not string, it will be converted to string. The method returns TRUE if length of the given string is in the required limits. Otherwise, the method returns FALSE.