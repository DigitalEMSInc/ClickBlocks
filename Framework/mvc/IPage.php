<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: page.php
 *
 * @category   MVC
 * @package    MVC
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\MVC;

use ClickBlocks\Cache\ICache;
use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Web\UI\Helpers;
use ClickBlocks\Core\Config;
use ClickBlocks\Core\Register;
use ClickBlocks\Web\Ajax;
use ClickBlocks\Web\CSS;
use ClickBlocks\Web\HTML;
use ClickBlocks\Web\JS;

interface IPage extends \ArrayAccess, \Countable
{
   public function getUniqueID();
   public function access();
   public function preparse();
   public function parse();
   public function init();
   public function load();
   public function unload();
   public function perform();
   public function show();
   public function render();
}
