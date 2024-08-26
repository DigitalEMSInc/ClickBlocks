# ClickBlocks\Utils\Strings

## General information

||
|-----------------|------------------------|
| **Inheritance** | no                     |
| **Subclasses**  | no                     |
| **Interfaces**  | no                     |
| **Source**      | Framework/utils/strings.php |

**Strings** contains the set of static methods for simplifying the work with strings.

## Public static methods ##

### **cut()**

```php
public static string cut(string $string, integer $length, boolean $word = true, boolean $stripTags = false, string $allowableTags = null)
```

|||
|--------------------|---------|----------------------------------------------------------------------------|
| **$string**        | string  | the given text to be reduced.                                              |
| **$length**        | integer | length of the shortened text.                                              |
| **$word**          | boolean | determines need to reduce the given text to the nearest word to the right. |
| **$stripTags**     | boolean | determines whether HTML and PHP tags will be deleted or not.               |
| **$allowableTags** | string  | specifies tags which should not be stripped.                               |

Cuts a large text. Example:

```php
$text = '<b>Lorem Ipsum</b> is simply <i>dummy</i> text of the printing and typesetting industry.';
echo Strings::cut($text, 25) . PHP_EOL;
echo Strings::cut($text, 25, false) . PHP_EOL;
echo Strings::cut($text, 25, true, true) . PHP_EOL;
echo Strings::cut($text, 25, true, true, '<i>');
```

The above example will output:

```
<b>Lorem Ipsum</b> is simply...
<b>Lorem Ipsum</b> is...
Lorem Ipsum is simply dummy...
Lorem Ipsum is simply <i>dummy</i>...
```