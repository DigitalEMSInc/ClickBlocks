namespace <?php echo $namespace; ?>;

use ClickBlocks\Core,
    ClickBlocks\Cache<?php if ($namespace != 'ClickBlocks\DB') echo ',' . PHP_EOL . '    ClickBlocks\DB'; echo ';'; ?>


/**
<?php echo $properties; ?>
 */
class <?php echo $class; ?> extends \ClickBlocks\DB\DALTable
{
    public function __construct()
    {
        parent::__construct('<?php echo addslashes($dbAlias); ?>', '<?php echo addslashes($logicTableName); ?>');
    }<?php echo $methods; ?>
}