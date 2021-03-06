<?php
/**
 * TablePrinter class.
 *
 * PHP version 5.3
 *
 * @category SLR
 * @package  SLR\Utils
 * @author   Krzysztof "Bushee" Nowaczyk <bushee01@gmail.com>
 * @license  BSD http://www.opensource.org/licenses/bsd-license.php
 * @link     http://bushee.ovh.org
 */

namespace SLR\Utils;

use SLR\Utils\Exception\UnknownBorderTypeException;

/**
 * TablePrinter class for printing any human readable table data.
 *
 * @category SLR
 * @package  SLR\Utils
 * @author   Krzysztof "Bushee" Nowaczyk <bushee01@gmail.com>
 * @license  BSD http://www.opensource.org/licenses/bsd-license.php
 * @link     http://bushee.ovh.org
 */
class TablePrinter
{
    /**
     * Vertical border.
     *
     * @const string BORDER_VERTICAL
     */
    const BORDER_VERTICAL = 'v';
    /**
     * Horizontal border.
     *
     * @const string BORDER_VERTICAL
     */
    const BORDER_HORIZONTAL = 'h';

    /**
     * Table data.
     *
     * @var array $data
     */
    protected $data;

    /**
     * Column widhts.
     *
     * @var array $colWidths
     */
    protected $colWidths;

    /**
     * Additional borders to use.
     *
     * @var array $borders
     */
    protected $borders;

    /**
     * Horizontal cell padding.
     *
     * @var int $padding
     */
    protected $padding;

    /**
     * Table width (column count).
     *
     * @var int $width
     */
    protected $width;

    /**
     * Table height (row count).
     *
     * @var int $height
     */
    protected $height;

    /**
     * Class constructor.
     * Allows to explicitly define minimal amount of rows and columns in table.
     *
     * @param int $padding Horizontal cell padding
     * @param int $width   Minimal width of table (amount of columns)
     * @param int $height  Minimal height of table (amount of rows)
     */
    public function __construct($padding = 2, $width = 0, $height = 0)
    {
        $this->data = array();
        $this->colWidths = array();
        $this->borders = array();
        $this->padding = $padding;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Adds value to cell.
     *
     * @param int    $x     X offset of cell
     * @param int    $y     Y offset of cell
     * @param string $value New cell value; will be cast to string anyway, but
     *                      passing string in the first place is advised
     *
     * @return void
     */
    public function cell($x, $y, $value)
    {
        // just to make sure
        $value = (string) $value;

        if (!isset($this->data[$x])) {
            $this->data[$x] = array();
        }
        $this->data[$x][$y] = $value;

        $width = strlen($value);
        if (!isset($this->colWidths[$x]) || $this->colWidths[$x] < $width) {
            $this->colWidths[$x] = $width;
        }

        $this->width = max($x + 1, $this->width);
        $this->height = max($y + 1, $this->height);
    }

    /**
     * Adds additional border to table.
     * This border will be added:
     * - on left side of specified column
     * - on top side of specified row
     *
     * @param int    $offset Offset of row/column
     * @param string $type   Offset type - whether to add border to row or column
     *
     * @return void
     *
     * @throws UnknownBorderTypeException When border of unknown type was to be added
     */
    public function addBorder($offset, $type = self::BORDER_VERTICAL)
    {
        if ($type == self::BORDER_HORIZONTAL || $type == self::BORDER_VERTICAL) {
            $this->borders[$type][$offset] = true;
        } else {
            throw new UnknownBorderTypeException($type);
        }
    }

    /**
     * Removes previously added additional border.
     *
     * @param int    $offset Offset of row/column
     * @param string $type   Offset type - whether to add border to row or column
     *
     * @see TablePrinter::addBorder
     *
     * @return void
     *
     * @throws UnknownBorderTypeException When border of unknown type was to be
     *                                    removed
     */
    public function removeBorder($offset, $type = self::BORDER_VERTICAL)
    {
        if ($type == self::BORDER_HORIZONTAL || $type == self::BORDER_VERTICAL) {
            unset($this->borders[$type][$offset]);
        } else {
            throw new UnknownBorderTypeException($type);
        }
    }

    /**
     * Sets horizontal cell padding.
     *
     * @param int $padding New padding value
     *
     * @return void
     */
    public function setPadding($padding)
    {
        $this->padding = $padding;
    }

    /**
     * Returns table width (in columns).
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Returns table height (in rows).
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Returns string representation of table.
     * Use this method to get human readable table data.
     *
     * @return string
     */
    public function __toString()
    {
        $s = '';

        for ($y = 0; $y < $this->height; ++ $y) {
            if (isset($this->borders[self::BORDER_HORIZONTAL][$y])) {
                for ($x = 0; $x < $this->width; ++ $x) {
                    if (isset($this->borders[self::BORDER_VERTICAL][$x])) {
                        $s .= '|';
                    }
                    $padding = $this->colWidths[$x] + $this->padding;
                    $s .= '|' . str_pad('', $padding, '-');
                }
                $s .= "|\n";
            }
            for ($x = 0; $x < $this->width; ++ $x) {
                if (isset($this->borders[self::BORDER_VERTICAL][$x])) {
                    $s .= '|';
                }
                $padding = $this->colWidths[$x] + $this->padding;
                $s .= '|' . str_pad(
                    $this->data[$x][$y], $padding, ' ', STR_PAD_BOTH
                );
            }
            $s .= "|\n";
        }

        return $s;
    }
}
