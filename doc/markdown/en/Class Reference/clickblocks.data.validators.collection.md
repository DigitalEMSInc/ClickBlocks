# ClickBlocks\Data\Validators\Collection #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/collection.php |

This validator compares the given array with another array. It can compare the array structures as well as verify their types (numeric, associative or mixed). The structure comparison can be strict or weak. In contrast to the weak compariosn the strict one verifies the order of elements in the array. Example:
```php
// Creates Collection validator.
$validator = new Validators\Collection();
// The array to be compared with. 
$validator->array = [1 => 'a', 2 => 'b', 3 => 'c'];
// We need strict comparison.
$validator->strict = true;
// We expect that the validating array must be numeric.
$validator->type = 'numeric';
// Verifies the given arrays.
$a = [1 => 'a', 2 => 'b', 3 => 'c'];
echo (int)$validator->validate($a); // output is 1.
$a = [2 => 'b', 1 => 'a', 3 => 'c'];
echo (int)$validator->validate($a); // output is 0.
$a = ['1' => 'b', 2 => 'a', 3 => 'c'];
echo (int)$validator->validate($a); // output is 0.
```

## Public non-static properties ##

### **array**

```php
public array $array
```

The array that is used as a sample for comparison.

### **strict**

```php
public boolean $strict = false
```

Determines whether the strict comparison is used. If the strict comparison is used, in addition to comparing key-value pairs the order of array elements will be taken into account.

### **type**

```php
public string $type
```

Determines the required type of the validating array. Possible values are "numeric", "associative" and "mixed".     The NULL value means no type checking.

### **validate()**

```php
public boolean validate(array $entity)
```

|||
|-|-|-|
| **$entity** | array | the array for validation. |

Validates a value. The method returns TRUE if the given array equal the another array or/and has the given type. Otherwise, the method returns FALSE.