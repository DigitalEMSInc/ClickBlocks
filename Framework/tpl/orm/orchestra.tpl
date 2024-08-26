namespace <?php echo $namespace; ?>;

use ClickBlocks\Core,
    ClickBlocks\Cache<?php if ($namespace != 'ClickBlocks\DB') echo ',' . PHP_EOL . '    ClickBlocks\DB'; echo ';'; ?>


class <?php echo $class; ?> extends \ClickBlocks\DB\Orchestra
{
    public function __construct()
    {
        parent::__construct('<?php echo $className; ?>');
    }

    public static function getBLLClassName()
    {
        return '<?php echo $className; ?>';
    }
}