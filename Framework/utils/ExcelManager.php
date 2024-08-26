<?php

namespace ClickBlocks\Utils;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Cell;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Color;
use PHPExcel_Style_Border;

/**
 * Class ExcelManager
 *
 * Our wrapper for PHPExcel. Set of convenient methods for build the project's Excel reports.
 */
class ExcelManager
{
    /**
     * Options of excel table
     */
    const
        OPTION_FONT_BOLD = 1,
        OPTION_FONT_SIZE = 2,
        OPTION_FONT_NAME = 3,
        OPTION_FONT_COLOR = 4,
        OPTION_ROW_HEIGHT = 5,
        OPTION_BACKGROUND_COLOR = 6,
        OPTION_BOTTOM_BORDER_COLOR = 7,
        OPTION_TOP_BORDER_COLOR = 8,
        OPTION_COLUMN_WITH_AUTO_SIZE = 9,
        OPTION_FONT_ITALIC = 10;

    /**
     * Style color
     */
    const
        STYLE_COLOR_RED = 'FF0000',
        STYLE_COLOR_GREEN = '006400',
        STYLE_COLOR_BLACK = '000000';

    /**
     * @var PHPExcel object of excel
     */
    private $excel;

    /**
     * Active sheet excel object
     *
     * @var PHPExcel_Worksheet
     */
    private $activeSheet;

    /**
     * Initialization class PHPExcel.
     *
     * If specified the file name, load it into the Excel object
     *
     * @param string $file file to load into Excel object
     */
    public function __construct(string $file = '')
    {
        if ($file) {
            $this->excel = PHPExcel_IOFactory::createReader('Excel2007')->load($file);
            $this->setActiveSheet(0);
        } else {
            $this->excel = new PHPExcel;
        }
    }

    /**
     * Get instance of PHPExcel
     * @return PHPExcel
     */
    public function getPhpExcelObject()
    {
        return $this->excel;
    }

    /**
     * The method creates sheets in object PHPExcel.
     *
     * @param string $name name sheet
     * @return $this
     */
    public function addSheet(string $name)
    {
        $firstSheet = $this->excel->getSheet(0);
        if ($this->excel->getSheetCount() === 1 && $firstSheet->getTitle() == 'Worksheet') {
            $this->activeSheet = $firstSheet->setTitle($name);
        } else {
            $newSheet = $this->excel->createSheet();
            $this->activeSheet = $newSheet->setTitle($name);
        }

        return $this;
    }

    /**
     * Return count sheets
     *
     * @return int
     */
    public function countSheet()
    {
        return $this->excel->getSheetCount();
    }

    /**
     * Return name sheet active sheet
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->excel->getActiveSheet()->getTitle();
    }

    /**
     * Return highest row active sheet
     *
     * @return int
     */
    public function getHighestRow()
    {
        return $this->activeSheet->getHighestRow();
    }

    /**
     * The method set active sheet by name.
     * @param string $name
     * @return $this
     */
    public function setActiveSheetByName(string $name)
    {
        $this->excel->setActiveSheetIndexByName($name);
        $this->activeSheet = $this->excel->getSheetByName($name);
        return $this;
    }

    /**
     * Return list sheets name
     *
     * @return string[]
     */
    /**
    public function getSheetNames()
    {
        return $this->excel->getSheetNames();
    }

    /**
     * The method set active sheet by index.
     *
     * @param int $index name sheet
     * @return $this
     */
    public function setActiveSheet(int $index)
    {
        $this->excel->setActiveSheetIndex($index);
        $this->activeSheet = $this->excel->getSheet($index);

        return $this;
    }

    /**
     * Get active worksheet of the Excel book
     * @return PHPExcel_Worksheet
     */
    public function getActiveSheet()
    {
        return $this->excel->getActiveSheet();
    }

    /**
     * The method insert image to Excel sheet
     *
     * @param string $coordinates coordinates for insert
     * @param string $path        path to file
     * @param int    $width       width of image
     * @param int    $height      height of image
     * @return $this
     */
    public function insertImage(string $coordinates, string $path, int $width, int $height = 0)
    {
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath($path);
        $objDrawing->setCoordinates($coordinates);
        if ($height) {
            $objDrawing->setResizeProportional(false);
            $objDrawing->setWidthAndHeight($width, $height);
        } else {
            $objDrawing->setWidth($width);
        }

        $objDrawing->setWorksheet($this->activeSheet);

        return $this;
    }

    /**
     * Set column widths
     *
     * @param array $widths array of widths column
     * @return $this
     */
    public function setColumnWidth(array $widths)
    {
        foreach ($widths as $column => $width) {
            $this->activeSheet->getColumnDimension($column)->setWidth($width);
        }

        return $this;
    }

    /**
     * Insert row to table
     *
     * @param array $rowValues   list of values
     * @param int   $rowNum      number of line
     * @param int   $startColumn column for start fill
     * @param array $options     options for setup row
     * @return $this;
     */
    public function insertRow(array $rowValues, int $rowNum, int $startColumn = 0, array $options = [])
    {
        $column = $startColumn;

        foreach ($rowValues as $item) {
            $coordinate = PHPExcel_Cell::stringFromColumnIndex($column) . $rowNum;
            $this->activeSheet->setCellValue($coordinate, $item);
            $style = $this->activeSheet->getStyle($coordinate);
            $font = $style->getFont();

            foreach ($options AS $option => $value) {
                switch ($option) {
                    case self::OPTION_FONT_BOLD:
                        $font->setBold((bool)$value);
                        break;
                    case self::OPTION_FONT_ITALIC:
                        $font->setItalic((bool)$value);
                        break;
                    case self::OPTION_FONT_SIZE:
                        $font->setSize((int)$value);
                        break;
                    case self::OPTION_FONT_NAME:
                        $font->setName($value);
                        break;
                    case self::OPTION_FONT_COLOR:
                        $font->getColor()->setRGB($value);
                        break;
                    case self::OPTION_ROW_HEIGHT:
                        $this->activeSheet->getRowDimension($rowNum)->setRowHeight((float)$value);
                        break;
                    case self::OPTION_BACKGROUND_COLOR:
                        $style->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($value);
                        break;
                    case self::OPTION_BOTTOM_BORDER_COLOR:
                        $color = new PHPExcel_Style_Color();
                        $color->setRGB($value);
                        $style->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $style->getBorders()->getBottom()->setColor($color);
                        break;
                    case self::OPTION_TOP_BORDER_COLOR:
                        $color = new PHPExcel_Style_Color();
                        $color->setRGB($value);
                        $style->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $style->getBorders()->getTop()->setColor($color);
                        break;
                    case self::OPTION_COLUMN_WITH_AUTO_SIZE:
                        $this->activeSheet
                            ->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($column))
                            ->setAutoSize($value);
                }
            }
            $column++;
        }

        return $this;
    }

    /**
     * Set the font color cell
     *
     * @param int    $column number column
     * @param int    $row    number row
     * @param string $color  font color
     * @return $this
     */
    public function setCellFontColor(int $column, int $row, string $color)
    {
        $activeSheet = $this->activeSheet;
        $coordinate = PHPExcel_Cell::stringFromColumnIndex($column) . $row;
        $activeSheet->getStyle($coordinate)->getFont()->getColor()->setRGB($color);

        return $this;
    }

    /**
     * Allows you to write an array of
     *
     * @param array $array Recording data
     * @param int $row record line number
     */
    public function insertArray(array $array, int $row)
    {
        $this->excel->setActiveSheetIndex(0)->fromArray($array, null, 'A' . $row);
    }

    /**
     * Start download excel file
     *
     * @param string $name name of file
     */
    public function downloadExcelFile(string $name)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * Start save excel file
     *
     * @param string $name name of file
     * @param string $path path of file
     */
    public function saveExcelFile(string $name, string $path)
    {
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save($path . $name . '.xlsx');
    }
}