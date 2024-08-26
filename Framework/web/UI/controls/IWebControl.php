<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;
use ClickBlocks\Web\UI\Helpers;

interface IWebControl extends Helpers\IControl
{
  public function parse(array &$attributes, Core\ITemplate $tpl);
  public function CSS();
  public function JS();
  public function HTML();
  public function getFullID();
  public function update($mode = true);
  public function redraw(array $parameters = null);
  public function delete();
}
