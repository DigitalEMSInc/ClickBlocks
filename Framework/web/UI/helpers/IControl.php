<?php

namespace ClickBlocks\Web\UI\Helpers;

interface IControl
{
  public function getParameters();
  public function setParameters(array $parameters);

  public function addClass($class);
  public function removeClass($class);
  public function replaceClass($class1, $class2);
  public function toggleClass($class1, $class2 = null);
  public function hasClass($class);

  public function addStyle($style, $value);
  public function setStyle($style, $value);
  public function getStyle($style);
  public function removeStyle($style);
  public function toggleStyle($style, $value);
  public function hasStyle($style);
}
