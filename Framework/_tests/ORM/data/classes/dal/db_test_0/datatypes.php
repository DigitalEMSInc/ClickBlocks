<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $ID
 * @property tinyint $tpTINYINT
 * @property smallint $tpSMALLINT
 * @property mediumint $tpMEDIUMINT
 * @property bigint $tpBIGINT
 * @property float $tpFLOAT
 * @property double $tpDOUBLE
 * @property decimal $tpDECIMAL
 * @property date $tpDATE
 * @property datetime $tpDATETIME
 * @property timestamp $tpTIMESTAMP
 * @property time $tpTIME
 * @property year $tpYEAR
 * @property char $tpCHAR
 * @property varchar $tpVARCHAR
 * @property tinyblob $tpTINYBLOB
 * @property blob $tpBLOB
 * @property mediumblob $tpMEDIUMBLOB
 * @property longblob $tpLONGBLOB
 * @property tinytext $tpTINYTEXT
 * @property text $tpTEXT
 * @property mediumtext $tpMEDIUMTEXT
 * @property longtext $tpLONGTEXT
 * @property enum $tpENUM
 * @property set $tpSET
 * @property binary $tpBINARY
 * @property varbinary $tpVARBINARY
 * @property bit $tpBIT
 * @property tinyint $tpBOOLEAN
 */
class DALDataTypes extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'DataTypes');
   }
}

?>