# ClickBlocks\DB\SQLExpression #

## General information ##

||
|-|-|
| **Inheritance**| no |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/db/core/sqlbuilder.php |

**SQLExpression** is a wrapper of any SQL expressions. If you want some SQL expression won't be processed during query building you should use this class:

```php
...
$sql = $builder->select('MyTable', '*')
               ->where(new SQLExpression('foo IS NOT NULL'))
               ->build();
// $sql will equal for MySQL engine:
// "SELECT * FROM `MyTable` WHERE foo IS NOT NULL"
```

## Public non-static methods ##

### **__construct()**

```php
public void __construct(string $sql)
```

|||
|-|-|-|
| **$sql** | string | any SQL expression. |

The class constructor.

### **__toString()**

```php
public string __toString()
```

Converts an object of this class to string.

## Protected non-static properties ##

### **sql** 

```php
protected string $sql = null
```

SQL expression.