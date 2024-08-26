# ClickBlocks\Web\POM\Iterator #

## General information ##

||
|-----------------|----|
| **Inheritance** | no |
| **Subclasses**  | no |
| **Interfaces**  | Iterator |
| **Source**      | Framework/web/pom/iterator.php |


This class is implementation of the control iterator that is used in all classes inherited from Panel class.

## Public non-static methods ##

### **__construct()**

```php
public void __construct(array $controls)
```

|||
|-|-|-|
| **$controls** | array | controls array to iterate. |

Class constructor. Initializes the private class properties.

### **rewind()**

```php
public void rewind()
```

Sets the internal pointer of the controls array to its first element.

### **current()**

```php
public boolean|ClickBlocks\Web\POM\Control current()
```

Returns the control object that's currently being pointed to by the internal pointer. If the internal pointer points beyond the end of the controls list or the control array is empty, it returns FALSE.

### **key()**

```php
public mixed key()
```

Returns the index element of the current array position. If the internal pointer points beyond the end of the controls list or the control array is empty, it returns NULL.

### **next()**

```php
public boolean|ClickBlocks\Web\POM\Control next()
```

Returns the control in the next place that's pointed to by the internal array pointer, or FALSE if there are no more controls.

### **valid()**

```php
public boolean valid()
```

Check if the current internal position is valid.