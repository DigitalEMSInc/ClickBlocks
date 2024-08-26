# ClickBlocks\Utils\UploadedFile

## General information

||
|-----------------|------------------------|
| **Inheritance** | no                     |
| **Subclasses**  | no                     |
| **Interfaces**  | no                     |
| **Source**      | Framework/utils/uploadedfile.php |

Helps to validate a file, uploaded through a form and to move it to the specified destination.

## Public non-static properties ##

### **name**

```php
public string $name
```

The desired name of the uploaded file. If this property is not defined the uploaded file will have the original name.

### **unique**

```php
public boolean $unique = false
```

Determines whether the name of the uploaded file should be unique.

### **destination**

```php
public string $destination
```

The desired destination directory for the uploaded file.

### **validate**

```php
public boolean $validate = true
```

Turns on or turns off the validation of the uploaded file during moving it to a new location.

### **extensions**

```php
public array $extensions = []
```

Allowable file extensions. The uploaded file should have extension which matches to one of these file extensions.

### **types**

```php
public array $types = []
```

Allowable mime types. The uploaded file should have mime type which matches to one of these types.

### **max**

```php
public integer $max
```

The maximum file size (in bytes) of the uploaded file. It should be greater than 0.

### **min**

```php
public integer min
```

The minimum file size (in bytes) of the uploaded file. It should be greater than 0.

## Public non-static methods ##

### **__construct()**

```php
public void __construct(string|array $path, string $name = null, string $type = null, integer $size = null, integer $error = null)
```

|||
|------------|---------------|------------------------------|
| **$path**  | string, array | the full temporary path to the file or data array of the uploaded file from `$_FILES`. |
| **$name**  | string        | the original file name.      |
| **$type**  | string        | the original file mime type. |
| **$size**  | integer       | the file size (in bytes).    |
| **$error** | integer       | the upload error.            |

Constructor. Accepts the information of the uploaded file as provided by the PHP global `$_FILES`.

### **getExtensionByType()**

```php
public string|null getExtensionByType()
```

Returns the extension based on the mime type. If the mime type is unknown, returns NULL.

### **getExtension()**

```php
public string getExtension()
```

Returns the extension of the uploaded file.

### **getType()**

```php
public string|null getType()
```

Returns the real mime type of the uploaded file. If the mime type is unknown, returns NULL.

### **getSize()**

```php
public integer getSize()
```

Returns the real size (in bytes) of the uploaded file.

### **getClientName()**

```php
public string getClientName()
```

Returns the original name of the uploaded file.

### **getClientType()**

```php
public string getClientType()
```

Returns the original mime type of the uploaded file.

### **getClientSize()**

```php
public integer getClientSize()
```

Returns the original size (in bytes) of the uploaded file.

### **getErrorCode()**

```php
public integer getErrorCode()
```

Returns the code of the last happened error.

### **getErrorMessage()**

```php
public string getErrorMessage()
```

Returns the message of the last happened error.

### **validate()**

```php
public boolean validate()
```

Checks that the uploaded file was uploaded successfully and match the specified requirements. Returns TRUE if no error occurred during uploading and all requirements are met and FALSE otherwise. Example:

```php
$file = new UploadedFile($_FILES['myfile']);
// Setting up necessary requirements.
$file->max = 1024 * 1024;
$file->extensions = ['png', 'gif', 'jpg'];
$file->types = ['image/png', 'image/gif', 'image/jpeg'];
// Checks the uploaded file.
if ($file->validate())
{ 
  // No errors.
  ...
}
else
{
  // Shows error message.
  echo $file->getErrorMessage();
}
```

### **move()**

```php
public array|boolean move()
```

Moves the uploaded file to a new location. Returns the information of the moved file and FALSE if an error was occurred. Example:

```php
$file = new UploadedFile($_FILES['myfile']);
// Setting up necessary requirements.
$file->max = 1024 * 1024;
$file->extensions = ['png', 'gif', 'jpg'];
$file->types = ['image/png', 'image/gif', 'image/jpeg'];
// Setting up new destination folder.
$file->destination = '/path/to/new/directory';
// Checks and moves the uploaded file.
if (false !== $info = $file->move())
{ 
  // No errors.
  print_r($info);
}
else
{
  // Shows error message.
  echo $file->getErrorMessage();
}
```

The above example can output the following result:

```
Array
(
    [path] => /path/to/myfile
    [name] => myfile
    [extension] => png
    [originalName] => originname
    [size] => 1234
    [type] => image/png
)
```

## Protected non-static properties ##

### **data**

```php
protected array $data = []
```

The information about the uploaded file.

### **error**

```php
protected integer $error = 0
```

The code of the last happened error.

### **defaultExtensions**

```php
protected array $defaultExtensions = []
```

The list of default extensions corresponding to file mime types.