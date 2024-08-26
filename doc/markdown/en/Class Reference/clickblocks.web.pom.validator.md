# ClickBlocks\Web\POM\Validator #

## General information ##

||
|-----------------|----|
| **Inheritance** | ClickBlocks\Web\POM\Control |
| **Subclasses**  | All classes of validators. |
| **Interfaces**  | ArrayAccess |
| **Source**      | Framework/web/pom/validators/validator.php |

**Validator** is the base class of all controls that can validate other controls. All validators have method **validate()** that used for validation of validator controls and abstract method **check()** that implements specific logic of validation control value according to the validator type.

The following attributes and properties are common for all validators:

### **Attributes**

| Name | Type | Default value | Description |
|-|-|-|-|
| **mode** | string | AND | determines logic of validation several controls. The valid values are: **AND** - all validating controls should be valid, **OR** - at least one validating control should be valid, **XOR** - only one validating control should be valid. |
| **index** | integer | 0 | determines order of launching of the validator. |
| **controls** | string | NULL | comma separated string containing unique or logic identifiers of the validating controls. |
| **groups** | string | NULL | comma separated string containing groups of the validator. |
| **state** | boolean | TRUE | validation status of the validator. |
| **locked**  | boolean | FALSE | determines whether the validator is locked or not. |
| **text** | string | NULL | the validator message. |
| **hiding** | boolean | FALSE | if it is TRUE, the validator will be hidden (via style "display") if it is not valid. Otherwise, the validator message will be removed if it is not valid. |

### **Properties**

| Name | Type | Default value | Description |
|-|-|-|-|
| **tag** | string | div | tag name of the validator element. |

## Public non-static methods ##

### **__construct()**

```php
public void __construct(string $id)
```

|||
|-|-|-|
| **$id** | string | the logic identifier of the validator. |

Class constructor. Initializes the validator attributes.

### **check()**

```php
abstract boolean function check($value)
```

|||
|-|-|-|
| **$value** | mixed | the value to validate. |

Returns TRUE if the given value is valid and FALSE if it isn't. This method is automatically invoked during the validation process.

### **getControls()**

```php
public array getControls()
```

Returns array of the validator control identifiers.

### **getGroups()**

```php
public array getGroups()
```

Returns array of the validator groups.

### **getResult()**

```php
public array getResult()
```

Returns the associative array of control validation statuses in which its keys are control unique identifiers and its values are control validation statuses.

### **setResult()**

```php
public self setResult(array $result)
```

|||
|-|-|-|
| **$result** | array | the associative array of control validation statuses. |

Sets result of validation of the validator controls. This method should be used inside custom validation function to set the validation result of the validator controls.

### **lock()**

```php
public self lock(boolean $flag = true)
```

|||
|-|-|-|
| **$flag** | boolean | determines whether the validator will be locked or unlocked. |

Locks the validator. The locked validator does not participate in the control validation.

### **isLocked()**

```php
public boolean isLocked()
```

Returns TRUE if the validator is locked and FALSE if it isn't.

### **clean()**

```php
public self clean()
```

Sets the validator status to valid.

### **validate()**

```php
public boolean validate()
```

Validates the validator controls according to the given mode (attribute "**mode**") and the validator type. The method returns TRUE if all validator controls are valid and FALSE otherwise.

### **render()**

```php
public string render()
```

Renders HTML of the validator.