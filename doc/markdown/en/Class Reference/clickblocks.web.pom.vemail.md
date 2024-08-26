# ClickBlocks\Web\POM\VEmail #

## General information ##

||
|-----------------|----|
| **Inheritance** | ClickBlocks\Web\POM\VRegExp |
| **Subclasses**  | no |
| **Interfaces**  | ArrayAccess |
| **Source**      | Framework/web/pom/validators/email.php |

This validator checks whether the value of the validating control matches valid email format. Besides of the common validator attributes **VEmail** has the following additional attributes:

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

Returns TRUE if the given value matches the email format and FALSE otherwise. If the given value is not scalar, the method always returns TRUE.

> By using the inverse prefix you can check a value for mismatch with the email format.