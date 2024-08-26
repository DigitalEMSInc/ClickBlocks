# ClickBlocks\Web\POM\VRequired #

## General information ##

||
|-----------------|----|
| **Inheritance** | ClickBlocks\Web\POM\Validator |
| **Subclasses**  | no |
| **Interfaces**  | ArrayAccess |
| **Source**      | Framework/web/pom/validators/required.php |

This validator checks whether the validating control has not empty value. Besides of the common validator attributes **VRequired** has the following additional attributes:

### **Attributes**

| Name | Type | Default value | Description |
|-|-|-|-|
| **trim** | boolean | FALSE | determines whether the checking value will be trimmed. |

## Public non-static method ##

### **check()**

```php
public boolean check(mixed $value)
```

|||
|-|-|-|
| **$value** | mixed | the value to validate. |

Returns TRUE if the given value is not empty and FALSE if it is empty. If the given value is not scalar, the method always returns TRUE.