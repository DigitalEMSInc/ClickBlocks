# ClickBlocks\Web\POM\VCompare #

## General information ##

||
|-----------------|----|
| **Inheritance** | ClickBlocks\Web\POM\Validator |
| **Subclasses**  | no |
| **Interfaces**  | ArrayAccess |
| **Source**      | Framework/web/pom/validators/compare.php |

This validator checks whether the values of the validating controls are equal to each other. Besides of the common validator attributes **VCompare** has the following additional attributes:

### **Attributes**

| Name | Type | Default value | Description |
|-|-|-|-|
| **caseInsensitive** | boolean | FALSE | determines whether the case insensitive comparison is used. |

## Public non-static method ##

### **check()**

```php
public string check(mixed $value)
```

|||
|-|-|-|
| **$value** | mixed | the value to validate. |

Returns the given value for comparison. If the given value is not scalar, the method throws exception.