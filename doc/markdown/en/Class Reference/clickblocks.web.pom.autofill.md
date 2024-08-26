# ClickBlocks\Web\POM\Autofill #

## General information ##

||
|-----------------|----|
| **Inheritance** | ClickBlocks\Web\POM\TextBox |
| **Subclasses**  | no |
| **Interfaces**  | ArrayAccess |
| **Source**      | Framework/web/pom/controls/autofill.php |

This control represents the Ajax autocomplete combobox that enables users to quickly find and select from a pre-populated list of values as they type. Besides of the common properties and attributes of the **TextBox** control, **Autofill** has the following additional properties and attributes:

### **Attributes**

| Name | Type | Default value | Description |
|-|-|-|-|
| **default** | mixed | NULL | the default value of the combobox. |
| **callback** | string | NULL | the delegate string that will be used as callback for populating values list of the combobox. |
| **timeout** | integer | 200 | the delay of reaction to user typing. |
| **activeItemClass** | string | NULL | the CSS class that will be applied to the active item in the values list. |

### **Properties**

| Name | Type | Default value | Description |
|-|-|-|-|
| **tag** | string | div | The HTML tag name of the container element of the control. |