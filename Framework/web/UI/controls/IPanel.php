<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

interface IPanel extends \IteratorAggregate, \ArrayAccess, \Countable
{
  public function isExpired();
  public function getControls();
  public function add(IWebControl $ctrl);
  public function insert(IWebControl $ctrl, $id = null);
  public function inject(IWebControl $ctrl, $id = null, $mode = Core\DOMDocumentEx::DOM_INJECT_TOP);
  public function delete($id, $isRecursion = false);
  public function deleteByUniqueID($uniqueID);
  public function replace($id, IWebControl $ctrl, $isRecursion = false);
  public function replaceByUniqueID($uniqueID, IWebControl $ctrl);
  public function get($id, $isRecursion = false);
  public function getByUniqueID($uniqueID);
}
