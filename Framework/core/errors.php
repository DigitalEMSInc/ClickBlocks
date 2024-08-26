<?php

// General messages
define('MSG_GENERAL_0', 'Sorry, server is not available at the moment. Please wait. This site will be working very soon!');

// General errors
define('ERR_GENERAL_0', 'Sorry the error occured, we\'re working on fixing it');
define('ERR_GENERAL_1', 'Class "[{var}]" is not found');
define('ERR_GENERAL_2', 'Invalid seek position ([{var}])');
define('ERR_GENERAL_3', 'Property "[{var}]" of class "[{var}]" does not exist');
define('ERR_GENERAL_4', 'Failed to open directory [{var}]');
define('ERR_GENERAL_5', 'Directory [{var}] does not exist');
define('ERR_GENERAL_6', 'getPage method of "[{var}]" class should return the instance of IPage interface');
define('ERR_GENERAL_7', 'Method "[{var}]" of class "[{var}]" does not exist.');
define('ERR_GENERAL_8', 'There has been a reference to an empty property of class [{var}]. Try to reset the cache.');
define('ERR_GENERAL_9', 'You don\'t have any permissions to perform "[{var}]" callback.');

// Config
define('ERR_CONFIG_1', 'The file "[{var}]" is not correct ini file');
define('ERR_CONFIG_2', 'Property "[{var}]" does not exists');

// Templates
define('ERR_TEMPLATE_1', 'Template with key "[{var}]" does not exist');
define('ERR_TEMPLATE_2', 'Unique key of a template can not be empty');
define('ERR_TEMPLATE_3', 'Parent template with key "[{var}]" does not exist');

// Cache
define('ERR_CACHE_1', 'Cache "[{var}]" does not exist');
define('ERR_CACHE_2', 'Cache "[{var}]" is not available');
define('ERR_CACHE_3', 'Cache directory "[{var}]" is not writable');
define('ERR_CACHE_4', 'APC cache extension is not enabled');
define('ERR_CACHE_5', 'APC cache extension is not enabled cli');
define('ERR_CACHE_6', 'XCache extension is not enabled');
define('ERR_CACHE_7', 'eAccelerator extension is not enabled');

// DAL classes
define('ERR_DAL_1', 'Incorrect value of property "[{var}]"');
define('ERR_DAL_2', 'It\'s impossible to rewrite autoincrement field "[{var}]"');
define('ERR_DAL_3', '');
define('ERR_DAL_4', 'Property "[{var}]" does not exist in table "[{var}]"');
define('ERR_DAL_5', 'Primary key of table "[{var}]" is already filled');
define('ERR_DAL_6', 'Primary key of table "[{var}]" is not filled yet');
define('ERR_DAL_7', 'Value of property "[{var}]" is too long');
define('ERR_DAL_8', 'Navigation Property "[{var}]" Foreign Keys mismatch. Mismatched values [{var}] vs [{var}]');
define('ERR_DAL_9', 'Method "[{var}]" does not exist');

// BLL classes
define('ERR_BLL_1', 'Property "[{var}]" does not exist in class "[{var}]"');
define('ERR_BLL_2', 'Method "[{var}]" does not exist in class "[{var}]"');
define('ERR_BLL_3', 'Value of navigation property "[{var}]" is not a valid BLL object');
define('ERR_BLL_4', 'Value of navigation property "[{var}]" is not an array');
define('ERR_BLL_5', 'Method "[{var}]" of "[{var}]" class can not be invoked directly');

// Services
define('ERR_SVC_1', 'The first argument should be an array, because Primary Key of [{var}] is multiple');
define('ERR_SVC_2', 'The second argument of method [{var}] is forbidden because only the working with db is hapened');
define('ERR_SVC_3', 'The BLL class "[{var}]" doesn\'t meet the Service "[{var}]"');

// Navigation properties
define('ERR_NAV_1', 'Navigation property "[{var}]" is not readable');
define('ERR_NAV_2', 'Navigation property "[{var}]" is not insertable');
define('ERR_NAV_3', 'Navigation property "[{var}]" is not deleteable');
define('ERR_NAV_4', 'Navigation property "[{var}]" is not updateable');
define('ERR_NAV_5', 'Method "[{var}]" of navigation property "[{var}]" does not exist');

// ORM Synchronizer
define('ERR_ORM_SYNC_1', 'XML file "[{var}]" for the synchronization is not found');
define('ERR_ORM_SYNC_2', 'Field [{var}] cannot be synchronized on both sides');

// Controls
define('ERR_CTRL_1', 'ID of "[{var}]" web control is empty');
define('ERR_CTRL_2', 'ID of "[{var}]" web control should contain only [a-aA-Z0-9] symbols, "[{var}]" was given');
define('ERR_CTRL_3', 'Only object implementing ClickBlocks\MVC\IPage can be value of "page" property');
define('ERR_CTRL_4', 'It\'s impossible to change uniqueID of web control "[{var}]" with ID = "[{var}]"');
define('ERR_CTRL_5', 'Web control "[{var}]" with ID = "[{var}]" exists already in the Panel with ID = "[{var}]"');
define('ERR_CTRL_6', 'Web control with ID = "[{var}]" hasn\'t the parent control, because it can not be deleted or replaced');
define('ERR_CTRL_7', 'Web control with uniqueID = "[{var}]" does not exist');
define('ERR_CTRL_8', 'Web control should be instance of a CheckBox control');
define('ERR_CTRL_9', 'Web control should be instance of a RadioButton or CheckBox control');
define('ERR_CTRL_10', 'Type of the navigator with ID = "[{var}]" is "[{var}]" and it is not implemented yet');
define('ERR_CTRL_11', 'Web control with ID = "[{var}]" is not instance of ClickBlocks\Web\UI\POM\Panel, because it can not include others controls');
define('ERR_CTRL_12', 'Injecting in a panel web control with ID = "[{var}]" can not be injected after or before this panel. Use a parent of the panel for injecting.');
define('ERR_CTRL_13', 'It\'s impossible to change parentUniqueID of web control "[{var}]" with ID = "[{var}]". To change parentUniqueID of some web control you should use setParent or setParentByUniqueID methods of web control object');
define('ERR_CTRL_14', 'Web control with ID = "[{var}]" does not exist');
define('ERR_CTRL_15', 'Web control should be instance of a CheckBox or RadioButton control');

// Validators
define('ERR_VAL_1', 'Validating control with ID = "[{var}]" not found');

// Events
define('ERR_EVN_1', 'Control with ID = "[{var}]" not found');

// XHTML/XML parsing
define('ERR_XHTML_1', 'HTML-Parser Error! [{var}] (file: [{var}]; line: [{var}]; column: [{var}])');
define('ERR_XHTML_2', 'Each template should be start with some control\'s tag, but not html tag');
define('ERR_XHTML_3', 'Web control with ID = "[{var}]" is not found');
define('ERR_XHTML_4', 'Web control with ID = "[{var}]" should be implemented IPanel, but its object type is "[{var}]"');
define('ERR_XHTML_5', 'Web control "[{var}]" with ID = "[{var}]" may not have "materpage" attribute because it is not the first web control in this template');

// HTML manager
define('ERR_HTML_1', 'Type "[{var}]" does\'nt exist. Type of inserting maybe only "top" or "bottom"');

// CSS manager
define('ERR_CSS_1', 'Type "[{var}]" does\'nt exist. Type of inserting maybe only "link" or "style"');

// JS manager
define('ERR_JS_1', 'Type "[{var}]" does\'nt exist. Type of inserting maybe only "link", "head", "foot" and "domready"');

// Pages
define('ERR_PG_1', 'The BODY element is not found!');
define('ERR_PG_2', 'You can save in the view state only web controls');

// DOM Extension
define('ERR_DOM_1', 'DOM Element with ID = "[{var}]" not found');

// Translate
define('ERR_TLT_1', 'Rule [[{var}]] contains unavailable constructions. A rule may contains only following constructions: "||", "&&", ",", "(", ")", "-", "+", "preg_match", digit characters and template variables');
define('ERR_TLT_2', 'Rule [[{var}]] contains unspecified template variable "[{var}]"');
