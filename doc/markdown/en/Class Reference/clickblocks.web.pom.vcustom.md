# ClickBlocks\Web\POM\VCustom #

## General information ##

||
|-----------------|----|
| **Inheritance** | ClickBlocks\Web\POM\Validator |
| **Subclasses**  | no |
| **Interfaces**  | ArrayAccess |
| **Source**      | Framework/web/pom/validators/custom.php |

This validator checks whether the values of the validating controls matches the user defined conditions. Besides of the common validator attributes **VCompare** has the following additional attributes:

### **Attributes**

| Name | Type | Default value | Description |
|-|-|-|-|
| **clientFunction** | string | NULL | the name of JS function, that implements checking of the user conditions on the client side. |
| **serverFunction** | string | NULL | the delegate that implements checking of the user conditions on the server side. |

## Public non-static method ##

### **check()**

```php
public mixed check(mixed $value)
```

|||
|-|-|-|
| **$value** | mixed | the value to validate. |

Returns the same value that passed to the method. 