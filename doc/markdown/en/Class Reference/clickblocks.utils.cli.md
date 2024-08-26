# ClickBlocks\Utils\CLI #

## General information ##

||
|-----------------|----|
| **Inheritance** | no |
| **Subclasses**  | no |
| **Interfaces**  | no |
| **Source**      | Framework/utils/cli.php |

**CLI** contains some helpful methods to work with command line.

## Public static methods ##

### **getArguments()**

```php
public static array getArguments(array $options)
```

|||
|--------------|-------|-------------------------------------------------------------|
| **$options** | array | names of the options. Each option can be an array of different names (aliases) of the same option. |

Returns values of command line options. Method returns FALSE if the script is not run from command line. For example, let's we have the following command: `myscript.php f 1 -f 2 --p v1 -p2 v2 p3 v3 p4 v4`. Then we will able to get necessary command parameters via the following code:

```php
$args = CLI::getArguments(['f', ['--p', '-p1', 'p1'], ['-p2', 'p2'], ['-p3', 'p3']]);
print_r($args);
```

The above example will output:

```
Array
(
    [f] => 1
    [--p] => v1
    [-p2] => v2
    [p3] => v3
)
```