# ClickBlocks\Web\POM\VRegExp #

## General information ##

||
|-----------------|----|
| **Inheritance** | ClickBlocks\Web\POM\Validator |
| **Subclasses**  | no |
| **Interfaces**  | ArrayAccess |
| **Source**      | Framework/web/pom/validators/regexp.php |

This validator checks whether the value of the validating control matches the given regular expression. Besides of the common validator attributes **VRegExp** has the following additional attributes:

### **Attributes**

| Name | Type | Default value | Description |
|-|-|-|-|
| **expression** | string | NULL | the regular expression (PCRE) to match with the checking value. | 
| **empty** | boolean | FALSE | if this parameter is set to TRUE, the validation of empty value will always return TRUE despite of the matching with the given regular expression. | 

## Public non-static method ##

### **check()**

```php
public boolean check(mixed $value)
```

|||
|-|-|-|
| **$value** | mixed | the value to validate. |

Returns TRUE if the given value matches the specified regular expression and FALSE otherwise. If the given value is not scalar, the method always returns TRUE.

> You can invert logic of any regular expression just by adding the inversion prefix **i** at the beginning of the given regular expression:

```php
// Creates validator and sets up the regular expression to check.
$val = new VRegExp('myval');
$val->expression = 'i/^[0-9]+$/';

// Checks the given values.
echo (int)$val->check('abc'); // will output 1
echo (int)$val->check('');    // will output 1
echo (int)$val->check('a7c'); // will output 1
echo (int)$val->check('123'); // will output 0
```