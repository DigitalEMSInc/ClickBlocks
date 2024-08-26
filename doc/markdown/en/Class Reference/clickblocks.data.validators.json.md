# ClickBlocks\Data\Validators\JSON #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/json.php |

The JSON validator compares the given JSON with another JSON or checks whether the given JSON has the specified structure. The logic of JSON schema validation is determined by [the relevant specification](http://json-schema.org/latest/json-schema-validation.html). Example:
```php
// Creates JSON validator.
$validator = new Validators\JSON();
// We will check JSON schema.
$validator->schema = '... some json schema goes here ...';
// Validates the given JSON.
$json = '... my json goes here ...';
echo (int)$validator->validate($json);
```

## Public non-static properties ##

### **json**

```php
public string $json
```

JSON string or path to the JSON file to be compared with.

### **schema**

```php
public string $schema
```

The JSON schema which the validating JSON should correspond to.

## Public non-static methods ##

### **validate()**

```php
public boolean validate(string $entity)
```

|||
|-|-|-|
| **$entity** | string | the JSON for validation. |

Validates a JSON string.