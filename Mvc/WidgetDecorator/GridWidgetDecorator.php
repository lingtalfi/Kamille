<?php


namespace Kamille\Mvc\WidgetDecorator;


use Kamille\Mvc\Widget\WidgetInterface;
use Kamille\Services\XLog;

/**
 * This decorator allows you to use a grid system to wrap your widgets.
 *
 *
 * If this can help (and I suppose it does help a lot), the model used is inspired from the grid system of bootstrap3.
 * http://getbootstrap.com/css/#grid
 *
 *
 *
 * Nomenclature
 * =================
 * The layout is seen as a stack of rows.
 * Each row can be divided in columns.
 *
 * By default, a widget uses only 1 column which spans the whole row width.
 *
 * We divide that row by using a fragment identifier.
 *
 * The fragment identifier defines how the row will be sliced.
 * For instance, if we bind a fragId of 1/2 to the first widget, it means that the row should be sliced in two equal columns,
 * and the widget should be  put in that first column.
 *
 * Once a row is opened (a division has been defined), subsequent widgets will be used to close the row,
 * even if no fragId is specified explicitly (an implicit version will be used automatically until the row is closed).
 *
 * Or you can specifically specify the row division if you prefer.
 *
 *
 * Basic concept
 * =================
 *
 * Widgets are read in order, so for instance, if we focus only on fragIds the widgets provide, we can
 * have a similar structure (which we will use a lot in this documentation, so take the time to understand it):
 *
 *
 * - 1/2
 * - 1/2
 *
 * The example above means that there are two widgets, the row is divided in two columns of equal width,
 * and each column holds one widget.
 *
 * Now if we do this:
 *
 * - 1/2
 * - (not specified)
 *
 * Notice that no fragId was specified for the second widget, therefore the second widget will be automatically
 * given a fragId of 1/2.
 *
 * Alike the previous example, if we do this:
 *
 * - 1/3
 * - (not specified)
 *
 * Since no fragId was specified on the second widget, it will fill the whole remaining space, and therefore
 * have an implicit fragId of 2/3.
 *
 * Below is how we would make three equal columns:
 *
 * - 1/3
 * - 1/3
 * - 1/3
 *
 * Below is how we would make two columns, the first taking 2/3 of the horizontal available space, and the last column
 * taking the remaining space:
 *
 *
 * - 2/3
 * -
 *
 * Or (equivalent to)
 *
 * - 2/3
 * - 1/3
 *
 * In practice, we generally don't need divisions lower than 1/3 (1/4 and below are generally not used).
 * In theory, we can go down to a 1/12 division.
 *
 *
 *
 * Children
 * ==============
 *
 * Things get a little bit more complicated when we need a more complex layout.
 * Imagine the following schema:
 *
 *
 * |---------------|---------------|---------------|
 * |       w1      |      w2       |      w3       |
 * |---------------|---------------|---------------|
 * |---------------|-------------------------------|
 * |               ||-----------------------------||
 * |               ||              w5             ||
 * |      w4       ||-----------------------------||
 * |               ||--------------|--------------||
 * |               ||       w6     |       w7     ||
 * |               ||--------------|--------------||
 * |---------------|-------------------------------|
 *
 *
 * Which actually translates to the following pseudo markup (which I will probably use
 * as of now, pure lazyness):
 *
 *
 * <row>
 *      <col 1/3> w1 </col>
 *      <col 1/3> w2 </col>
 *      <col 1/3> w3 </col>
 * </row>
 * <row>
 *      <col 1/3> w4 </col>
 *      <col 2/3>
 *          <row>
 *              <col 1/1> w5 </col>
 *          </row>
 *          <row>
 *              <col 1/2> w6 </col>
 *              <col 1/2> w7 </col>
 *          </row>
 *      </col>
 * </row>
 *
 *
 *
 *
 *
 *
 *
 *
 * Here is how we would notate the above schema:
 *
 * - w1: 1/3
 * - w2: 1/3
 * - w3: (blank, or 1/3)
 * - w4: 1/3
 * - w5: 2/3-1
 * - w6: 1/2
 * - w7: 1/2.
 *
 *
 * Did you notice that we introduced two new syntax elements: the dash (-), and the dot(.).
 * Those are complementary, they mean: open nested level and close nested level respectively.
 * To better understand this, we will focus on the pseudo markup notation.
 *
 *
 * The dash
 * ------------
 * Understand that widgets are parsed in their order of appearance in the config file.
 *
 * When two consecutive widgets are separated by the opening of a new row, then we use a dash between
 * the two fragIds (the fragId before the opening row, and the fragId after).
 * That's what we have in the above example with w5: 2/3-1.
 * w4 had 1/3, and w5 opens a nested level (col 2/3), plus a column (1/1).
 * If we combine the two together and separate them with a dash, we obtain this: 2/3-1/1.
 * However, the 1/1 is replaced by 1 for brevity, so our fragId for w5 becomes 2/3-1.
 *
 *
 *
 * Note:
 * A column only must be the direct children of a row.
 * This means a row cannot be the direct parent of a row, which is why it's not possible to have double dash (consecutive dash)
 * in this notation.
 *
 *
 * The dot
 * -----------
 * When you open a nested level, you must at some point close it.
 * Since you can stack any number of rows in a column, you need to specify WHEN that stack ends,
 * and you do so using the dot.
 * It works exactly like the dash, but in reverse.
 * So in the above example again, w7 specifies a fragId of 1/2., which means close the nested columns started at w5.
 *
 *
 *
 *
 *
 * Here is one more example of structure and corresponding notation.
 *
 *
 * <row>
 *      <col 1/3> w1 </col>
 *      <col 1/3> w2 </col>
 *      <col 1/3> w3 </col>
 * </row>
 * <row>
 *      <col 1/6> w4 </col>
 *      <col 4/6>
 *          <row>
 *              <col 1/1> w5 </col>
 *          </row>
 *          <row>
 *              <col 1/3> w6 </col>
 *              <col 1/3>
 *                  <row>
 *                      <col 1/1> w7 </col>
 *                  </row>
 *              </col>
 *              <col 1/3> w8 </col>
 *          </row>
 *          <row>
 *              <col 1/1> w9 </col>
 *          </row>
 *      </col>
 *      <col 1/6> w10 </col>
 * </row>
 *
 *
 * which would be represented by the following notation:
 *
 *
 * - w1: 1/3
 * - w2: 1/3
 * - w3: 1/3
 * - w4: 1/6
 * - w5: 4/6-1
 * - w6: 1/3
 * - w7: 1/3-1.
 * - w8: 1/3
 * - w9: 1.
 * - w10: 1/6
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
class GridWidgetDecorator implements WidgetDecoratorInterface
{

    /**
     * @var array,
     *
     * If an array, contains one "info" per level.
     * The top level has index 0.
     *
     * New levels are APPENDED to the array, and popped out as there is vertical/hierarchical movement.
     *
     * The info is an array with the following structure:
     *      0: the max space available
     *      1: the current space used
     *
     *
     * An info array is only available when it's entered (i.e. when the first widget of the level is being processed).
     *
     *
     *
     *
     */
    private $levelSpaceInfo;
    private $currentLevel;
    private $systemStarted;
    private $lastColumnWasLastOfRow;
    private $isInitialized;
    private $lastWidgetOfPosition;


    public function __construct()
    {
        $this->levelSpaceInfo = [];
        $this->currentLevel = 0;
        $this->systemStarted = false;
        $this->lastColumnWasLastOfRow = false;
        $this->isInitialized = false;
    }


    public static function create()
    {
        return new static();
    }

    public function decorate(&$content, $positionName, $widgetId, $index, WidgetInterface $widget, array $config)
    {
        if (true === $this->isGridSystemActive($positionName, $config)) {

            $this->init($positionName, $config);


            $createExtraLevel = false;


            // get the current level
            $parentFragId = "";
            $fragId = $this->getFragId($widgetId, $config);
            if (false !== strpos($fragId, '-')) {
                $p = explode('-', $fragId, 2);
                $parentFragId = $p[0];
                $parentFragId = "";
                $createExtraLevel = true;
                $this->currentLevel++;
            }
            $curLevel = $this->currentLevel;


            if (false !== ($levelInfo = $this->getLevelInfo($curLevel, $fragId))) {

                list($currentSpaceUsed, $maxSpaceAvailable) = $levelInfo;


                $closeRow = false;
                $closeNestedRow = false;
                $this->lastColumnWasLastOfRow = false;


                /**
                 * compute the space information
                 */
//                $eatenSpace = $this->getEatenSpace($fragId);
//                $currentSpaceUsed += $eatenSpace;
//
//                $isClosing = false;
//                if ($currentSpaceUsed >= $maxSpaceAvailable) {
//                    $isClosing = true;
//                    if ($currentSpaceUsed > $maxSpaceAvailable) {
//                        XLog::error("invalid fragId specified: eating too much space! Eating $eatenSpace cols while there is only $maxSpaceAvailable cols available");
//                    }
//                }


                // wrapping content
                $sp = str_repeat("&nbsp;", 8);
                $debug = " - [used: $currentSpaceUsed, max= $maxSpaceAvailable]<br>";


                $sPrefix = "";
                if (false === $this->systemStarted) {
                    $sPrefix .= "[row]: starter<br>";
                    $this->systemStarted = true;
                }
                if (true === $createExtraLevel) {
                    $sPrefix .= "[row]: extra level<br>";
                }
                $sPrefix .= $sp . "[col $fragId]";
                $sSuffix = "";
                $sSuffix .= $sp . "[/col]<br>";


                $s = "";
                $s .= $sPrefix . $debug . $content . $sSuffix;
                $content = $s;


                $this->setLevelInfo($curLevel, $currentSpaceUsed, $maxSpaceAvailable);
                // update the level information


//                if (true === $isClosing) {
//                    $this->lastColumnWasLastOfRow = true;
//                }

            }
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getFragId($widgetId, array $config)
    {
        if (
            array_key_exists("widgets", $config) &&
            array_key_exists($widgetId, $config['widgets']) &&
            array_key_exists("grid", $config['widgets'][$widgetId])
        ) {
            return $config['widgets'][$widgetId]['grid'];
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function isGridSystemActive($positionName, array $config)
    {
        if (array_key_exists("grid", $config)) {
            $grid = $config['grid'];
            if (!is_array($grid)) {
                $grid = [$grid];
            }
            if (in_array($positionName, $grid, true)) {
                return true;
            }
        }
        return false;
    }

    private function getLevelInfo($level, $fragId)
    {
        if (array_key_exists($level, $this->levelSpaceInfo)) {
            return $this->levelSpaceInfo[$level];
        } else {
            // assert this is the first call of the "session"
            $p = explode('-', $fragId, 2);
            $l = array_pop($p);
            $l = rtrim($l, ".");
            if ('1' === $l) {
                $info = [0, 1];
            } else {
                $p = explode('/', $l, 2);
                $info = [$p[0], $p[1]];
            }
            $this->levelSpaceInfo[$level] = $info;
            return $info;

        }
        XLog::error("GridWidgetDecorator: cannot access levelInfo for level $level, with fragId=$fragId");
        return false;
    }

    private function getEatenSpace($fragId)
    {
        $p = explode('-', $fragId, 2);
        $l = array_pop($p);
        $p = explode('/', $l, 2);
        return $p[0];
    }


    private function setLevelInfo($level, $currentSpaceUsed, $maxAvailableSpace)
    {
        $this->levelSpaceInfo[$level] = [$currentSpaceUsed, $maxAvailableSpace];
        return $this;
    }

    private function init($positionName, array $config)
    {
        if (false === $this->isInitialized) {
            $this->isInitialized = true;
            az($positionName);
            $positions = $config['widgets'];
            foreach($positions as $id => $info){

            }


            $this->lastWidgetOfPosition = 0;
        }
    }
}