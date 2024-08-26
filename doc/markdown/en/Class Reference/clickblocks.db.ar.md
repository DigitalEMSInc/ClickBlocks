# ClickBlocks\DB\AR #

## General information ##

||
|-|-|
| **Inheritance** | no |
| **Child classes** | no |
| **Interfaces** | no |
| **Source** | Framework/db/core/ar.php |

AR is the base class that implements the active record design pattern. This means that using by this class, you can easily work with one record of the database table (or view). At that, the properties of the class correspond to the table columns.

The class allows you to insert new record to the database table, update and remove existing records. Besides, you can retrieve any data from the current database table using method **select** and **count**. There is also ability to get data from related tables via foreign keys of the active record table and method **relation**.

## Public static properties ##

### **connection**

```php
public static ClickBlocks\DB\DB $connection
```

The default database connection object for all active record classes.

### **cacheExpire**

```php
public static integer $cacheExpire
```

Default lifetime (in seconds) of the table metadata cache. This property contains the default value of the cache lifetime for all instances of AR class.

The caching metadata does not occur if **$cacheExpire** equals FALSE or 0.

If **$cacheExpire** less than 0, the cache lifetime will equal the database cache vault lifetime.

### **cacheGroup**

```php
public static string $cacheGroup
```

Default cache group of all cached table metadata.

## Public non-static method ##

### **__construct()**

```php
public void __construct(string $table, ClickBlocks\DB\DB $db = null, integer $cacheExpire = null, string $cacheGroup = null)
```

|||
|-|-|-|
| **$table** | string | the database table. |
| **$db** | ClickBlocks\DB\DB | the database connection object. |
| **$cacheExpire** | integer | the lifetime of the table metadata cache. |
| **$cacheGroup** | integer | the group name of the table metadata cache. |

The class constructor. Gets all table metadata and put them into the database cache.

If **$db** is empty then the default database connection object specified in static property **$connection** will be used. If **$connection** is not set the value of global variable "db" (previously stored via `\CB::set`) is used.

By default the cache expiration time and cache group are taken from the configuration file, from section "ar":

```ini
[ar]
cacheExpire = 3600
cacheGroup  = "ar"
```

However the values of these parameters can be overwritten through the appropriate method parameters or class static properties. At that, the method parameters have higher priority than the class properties, and the class property have higher priority than the configuration settings.

if **$cacheExpire** is equal to FALSE means that the table metadata is not cached. If **$cacheExpire** less than 0, the cache lifetime will equal the database cache vault lifetime.

### **getTable()**

```php
public string getTable()
```

Returns the name of the database table of the current active record object.

### **getInfo()**

```php
public array getInfo(string $entity = null)
```

|||
|-|-|-|
| **$entity** | string | determines the type of necessary metadata (can be "table" or "columns"). If this parameter is null the method returns all metadata. |

Returns meta-information about the database table or its columns. Method returns FALSE if metadata for the given entity doesn't exist or entity is invalid.

### **getColumnInfo()**

```php
public mixed getColumnInfo(string $column, string $entity = null)
```

|||
|-|-|-|
| **$column** | string | the column name. |
| **$entity** | string | determines the type of necessary metadata. If this parameter is null the method returns all metadata. |

Returns the meta-information about the table column. If **$entity** is invalid the method returns FALSE.

### **isPrimaryKey()**

```php
public boolean isPrimaryKey(string $column)
```

|||
|-|-|-|
| **$column** | string | - the column name. |

Return TRUE if the given column is part of the table primary key and FALSE otherwise.

### **isAutoincrement()**

```php
public boolean isAutoincrement(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns FALSE if the given column is an autoincrement (ID) column and FALSE otherwise.

### **isNullable()**

```php
public boolean isNullable(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns TRUE if the given column is nullable and FALSE otherwise.

### **isUnsigned()**

```php
public boolean isUnsigned(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns TRUE if the given column can take only unsigned numbers and FALSE otherwise.

### **getColumnType()**

```php
public string getColumnType(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns DBMS data type of the given column.

### **getColumnPHPType()**

```php
public string getColumnPHPType(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns PHP data type of the given column.

### **getDefaultValue()**

```php
public mixed getDefaultValue(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns default value of the given column.

### **getMaxLength()**

```php
public integer getMaxLength(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns maximum length of the given column.

### **getPrecision()**

```php
public integer getPrecision(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns precision (the number digits after comma) of the given column.

### **getEnumeration()**

```php
public array getEnumeration(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns enumeration values of the given column.

### **exp()**

```php
public ClickBlocks\DB\SQLExpression exp(string $sql)
```

|||
|-|-|-|
| **$sql** | string | any SQL string. |

Converts the given string to **ClickBlocks\DB\SQLExpression** object.

### **getValues()**

```php
public array getValues()
```

Returns column values of the current table row.

### **setValues()**

```php
public self setValues(array $values, boolean $ignoreNonExistingColumns = true)
```

|||
|-|-|-|
| **$values** | array | new column values. |
| **$ignoreNonExistingColumns** | boolean | determines whether it is necessary to ignore non-existing columns during setting of their new values. |

Assigns new values to the table columns.

### **isAssigned()**

```php
public boolean isAssigned()
```

Returns TRUE if if the active record object was initiated from the database (or array) and FALSE otherwise.

### **isChanged()**

```php
public boolean isChanged()
```

Returns TRUE if at least one column value was changed and FALSE otherwise.

### **isDeleted()**

```php
public boolean isDeleted()
```

Returns TRUE if the current record was deleted and FALSE otherwise.

### **isPrimaryKeyFilled()**

```php
public boolean isPrimaryKeyFilled(boolean $insert = false)
```

|||
|-|-|-|
| **$insert** | boolean | if TRUE the autoincrement primary key column will be ignored during the checking. |

Returns TRUE if primary key columns are filled and FALSE otherwise.

### **__isset()**

```php
public boolean __isset(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns TRUE if a column with the given name exists and FALSE otherwise.

### **__get()**

```php
public mixed __get(string $column)
```

|||
|-|-|-|
| **$column** | string | the column name. |

Returns the column value.

### **__set()**

```php
public void __set(string $column, mixed $value)
```

|||
|-|-|-|
| **$column** | string | the column name. |
| **$value** | mixed | the column value. |

Sets new value of the column.

### **assign()**

```php
public ClickBlocks\DB\AR assign(mixed $where, mixed $order = null)
```

|||
|-|-|-|
| **$where** | mixed | the WHERE clause condition. |
| **$order** | mixed | the ORDER BY clause condition. |

Finds record in the table by the given criteria and assign column values to the properties of the active record object.

### **assignFromArray()**

```php
public ClickBlocks\DB\AR assignFromArray(array $columns)
```

|||
|-|-|-|
| **$columns** | array | the associative array of column values. |

Initializes the active record object by array values. Keys of **$columns** should match the table column names. Array elements whose keys do not match the table columns will be ignored.

### **reset()**

```php
public ClickBlocks\DB\AR reset()
```

Turns the active record object to uninitialized state. All columns gets their default values.

### **save()**

```php
public integer save(array $options = null)
```

|||
|-|-|-|
| **$options** | array | contains additional parameters (for example, **updateOnKeyDuplicate** or **sequenceName**) required by some DBMS. |

Updates row in the database table if the current active record object was initialized or inserts new row to the table otherwise. The method returns numbers of the affected rows.

### **insert()**

```php
public integer insert(array $options = null)
```

|||
|-|-|-|
| **$options** | array | contains additional parameters (for example, **updateOnKeyDuplicate** or **sequenceName**) required by some DBMS. |

Inserts new row to the database table. The method returns numbers of the affected rows.

### **update()**

```php
public integer update(mixed $where = null)
```

|||
|-|-|-|
| **$where** | mixed | information about conditions of the updating. |

If the single parameter is not set the method updates the current active row in the database table. Otherwise, the method updates row or rows in the table according to the given WHERE condition.

The method returns numbers of the affected rows.

### **delete()**

```php
public integer delete(mixed $where = null)
```

|||
|-|-|-|
| **$where** | mixed | information about conditions of the deleting. |

If the **$where** is not set the method deletes the current active row from the database table. Otherwise, the method deletes row or rows from the table according to the given WHERE condition.

The method returns numbers of the affected rows.

### **count()**

```php
public integer count(mixed $where = null)
```

|||
|-|-|-|
| **$where** | mixed | the WHERE clause information. |

Returns the total number of rows in the database table if **$where** is not set. Otherwise, the method returns the total number of rows according to the given WHERE condition.

### **select()**

```php
public mixed select(mixed $columns = '*', mixed $where = null, mixed $order = null, integer $limit = null, integer $offset = null)
```

|||
|-|-|-|
| **$columns** | mixed | the columns to be extracted from the table. |
| **$where** | mixed | the WHERE clause information. |
| **$order** | mixed | the ORDER BY clause information. |
| **$limit** | integer | the maximum number of rows to be retrieved. |
| **$offset** | integer | the offset of the first extracted row. |

Finds rows in the database table according to the given criteria.

### **relation()**

```php
public array relation(string $relation, mixed $columns = '*', mixed $order = null, integer $limit = null, integer $offset = null)
```

|||
|-|-|-|
| **$relation** | string | the name of the table constraint (foreign key) or its index number. |
| **$columns** | mixed | the columns to be extracted from the table. |
| **$order** | mixed | the ORDER BY clause information. |
| **$limit** | integer | the maximum number of rows to be retrieved. |
| **$offset** | integer | the offset of the first extracted row. |

Returns data related with the current database table according to the given constraint name and search criteria.

## Protected static properties ##

### **info**

```php
protected static array $info = []
```

Contains metadata of all database tables.

## Protected non-static properties ##

### **db**

```php
protected ClickBlocks\DB\AR $db = null
```

The database connection object.

### **table**

```php
protected string $table = null
```

The name of the table for the current active record object.

### **columns**

```
protected array $columns = []
```

Contains values of the table columns for the current active record.

### **pk**

```php
protected array $pk = []
```

Contains the list of the primary key column names.

### **ai**

```php
protected string $ai = null
```

Contains the name of the auto-increment column.

### **assigned**

```php
protected boolean $assigned = false
```

Determines whether the active record object is initiated from the database.

### **changed**

```php
protected boolean $changed = false
```

Determines whether at least one column value is changed.

### **deleted**

```php
protected boolean $deleted = false
```

Determines whether the current active record is deleted.