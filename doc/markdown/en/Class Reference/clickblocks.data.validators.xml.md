# ClickBlocks\Data\Validators\XML #

## General information ##

||
|-|-|
| **Inheritance** | ClickBlocks\Data\Validators\Validator |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/data/validators/xml.php |

By using XML validator you can easily compare the given XML with another XML or checks whether the given XML has the specified structure. Example:
```php
// Creates XML validator.
$validator = new Validators\XML();
// Sets path to the sample XML file.
$validator->xml = '/path/to/xml';
// Sets the XML schema to matched with.
$validator->schema = '/path/to/xml/schema';
// Validation.
$xml = '... some XML goes here ...';
echo (int)$validator->validate($xml);
```

## Public non-static properties ##

### **xml**

```php
public string $xml
```

XML string or path to the XML file to be compared with.

### **schema**

```php
public string $schema
```

The XML schema or path to the XML schema which the validating XML should correspond to.

## Public non-static methods ##

### **validate()**

```php
public boolean validate(string $entity)
```

|||
|-|-|-|
| **$entity** | string | some XML string to validate. |

Validates an XML string. It returns TRUE if the given XML matches with the sample XML or/and has the specific schema. Otherwise, it returns FALSE.