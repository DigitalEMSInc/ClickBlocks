# ClickBlocks\Utils\Arrays #

## General information ##

||
|-----------------|----|
| **Inheritance** | no |
| **Subclasses**  | no |
| **Interfaces**  | no |
| **Source**      | Framework/utils/arrays.php |

**Arrays** is an utility class that contains a few static methods simplifying the frequently used operations with arrays.

## Public static methods ##

### **swap()**

```php
public static void swap(array &$array, integer $n1, integer $n2)
```

|||
|------------|---------|------------------------------------------------------|           
| **$array** | array   | the array in which the two elements will be swapped. |
| **$n1**    | integer | the index number of the first element.               |
| **$n2**    | integer | the index number of the second element.              |

Swaps the two elements of the array. The elements are determined by their index numbers.

### **aswap()**

```php
public static void aswap(array &$array, mixed $key1, mixed $key2)
```

|||
|------------|-------|------------------------------------------------------|
| **$array** | array | the array in which the two elements will be swapped. |
| **$key1**  | mixed | the key of the first element.                        |
| **$key2**  | mixed | the key of the second element.                       |

Swaps the two elements of the array. The elements are determined by their keys.

### **insert()**

```php
public static void insert(array &$array, mixed $value, integer $offset = 0)
```

|||
|-------------|---------|----------------------------------------------------|
| **$array**  | array   | the input array in which a value will be inserted. |
| **$value**  | mixed   | the inserting value.                               |
| **$offset** | integer | the position in the first array.                   |

Inserts a value or an array to the input array at the specified position. Example:

```php
$a = ['a' => 1, 'b' => 2, 'c' => 3];     // the input array.
Arrays::insert($a, ['d' => 4], 1); // $a is ['a' => 1, 'd' => 4, 'b' => 2, 'c' => 3]
Arrays::insert($a, [1, 2, 3], 4);  // $a is ['a' => 1, 'd' => 4, 'b' => 2, 'c' => 3, 0 => 1, 1 => 2, 2 => 3] 
```

### **unsetByKeys()**

```php
public static void unsetByKeys(array &$array, array $keys)
```

|||
|------------|-------|--------------------------------------------------|
| **$array** | array | the array from which an element will be removed. |
| **$keys**  | array | the element keys.                                |

Completely removes the element of a multidimensional array, defined by its keys. Example:

```php
// the input array.
$a = ['k1' => ['k11' => 'a', 'k12' => 'b', 'k13' => 'c'],
      'k2' => ['k21' => ['k211' => 1], 'k22' => 2],
      'k3' => 3];
      
Arrays::unsetByKeys($a, ['k1', 'k12']);
print_r($a);
Arrays::unsetByKeys($a, ['k2', 'k21']);
print_r($a);
Arrays::unsetByKeys($a, ['k1']);
print_r($a);
```

The above example will output:

```
Array
(
    [k1] => Array
        (
            [k11] => a
            [k13] => c
        )

    [k2] => Array
        (
            [k21] => Array
                (
                    [k211] => 1
                )

            [k22] => 2
        )

    [k3] => 3
)
Array
(
    [k1] => Array
        (
            [k11] => a
            [k13] => c
        )

    [k2] => Array
        (
            [k22] => 2
        )

    [k3] => 3
)
Array
(
    [k2] => Array
        (
            [k22] => 2
        )

    [k3] => 3
)
```