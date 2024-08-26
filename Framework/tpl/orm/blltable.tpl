namespace <?php echo $namespace; ?>;

use ClickBlocks\Core,
    ClickBlocks\Cache<?php if ($namespace != 'ClickBlocks\DB') echo ',' . PHP_EOL . '    ClickBlocks\DB'; echo ';'; ?>


/**
<?php echo $properties; ?>
 */
class <?php echo $class; ?> extends <?php echo $parent.PHP_EOL; ?>{
    public function __construct()
    {
        parent::__construct();
        $this->addDAL(new <?php echo $dalclass; ?>(), __CLASS__);
    }<?php echo $methods; ?>
}