<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class HeadLink extends Control
{
    public function __construct($attributes)
    {
        parent::__construct(null);
        foreach ($attributes AS $name => $value) {
            $this->attributes[$name] = $value;
        }
    }

    public function render()
    {
        return '<link' . $this->getParams() . ' />';
    }
}

?>
