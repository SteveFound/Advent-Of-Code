<?php
declare(strict_types = 1);

/**
 * What can be in a grid cell
 */
enum GridCell
{
    case Empty;
    case Rock;
    case Sand;
}

/**
 * Find the minimum and maximum rock coordinates 
 * class: MinMax
 */
class MinMax
{
    public int $minX = PHP_INT_MAX;
    public int $maxX = PHP_INT_MIN;
    public int $minY = PHP_INT_MAX;
    public int $maxY = PHP_INT_MIN;

    /**
     * update : Update the min and max coordinates from a string coordinate expressed as "x,y"
     * @param string $coord
     * 
     * @return void
     */
    public function update(string $coord)
    {
        // new toy, this reads the array values (as strings) straight into variables.
        list($xc,$yc) = explode(",", trim($coord));
        $x = (int)$xc;
        $y = (int)$yc;
        $this->minX = min($this->minX, $x);
        $this->minY = min($this->minY, $y);
        $this->maxX = max($this->maxX, $x);
        $this->maxY = max($this->maxY, $y);
    }

    public function __toString()
    {
        return "[MinMax: Min:(" . $this->minX . "," . $this->minY . ") Max:(" . $this->maxX . "," . $this->maxY . ")]";
    }
}

/**
 * The grid class. This will be passed to other classes that need to read and set the grid contents.
 * class: Grid
 */
class Grid
{
    private array $grid;
    private int $width;
    private int $height;

    public function __construct()
    {
        $this->grid = [];
        $this->width = 0;
        $this->height = 0;
    }

    /**
     * getWidth : Get the width of the grid
     * 
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * getHeight : Get the height of the grid
     * 
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * allocate : Allocate a grid of empty cells. We always allocate from 0,0 so maxX and maxY give the required grid dimensions.
     * @param int $maxX
     * @param int $maxY
     * 
     * @return void
     */
    public function allocate( int $maxX, int $maxY )
    {
        // maxX and maxY are 0 based, so add 1.
        $this->height = $maxY + 1;
        $this->width = $maxX + 1;

        for( $r = 0; $r < $this->height; $r++ ) {
            $this->grid[$r] = array_fill(0, $this->width, GridCell::Empty);
        }
    }

    /**
     * extend : Extend the existing grid by dx and dy empty cells.
     * @param int $dx
     * @param int $dy
     * 
     * @return void
     */
    public function extend( int $dx, int $dy ): void
    {
        for( $r = 0; $r < $this->height; $r++ ){
            $this->grid[$r] = array_merge($this->grid[$r], array_fill(0, $dx, GridCell::Empty));
        }
        $this->width += $dx;
        for( $r = $this->height; $r < $this->height + $dy; $r++) {
            $this->grid[$r] = array_fill(0, $this->width, GridCell::Empty);
        }
        $this->height += $dy;
    }

    /**
     * getCell : Get the contents of a cell, error if it's out of bounds.
     * @param int $x
     * @param int $y
     * 
     * @return GridCell
     * @throws Exception
     */
    public function getCell( int $x, int $y ): GridCell
    {
        if( $y >= $this->height ){
            throw new Exception( "getCell(" . $x . "," . $y . ") " . $y . " off grid. [" . $this->width . "," . $this->height. "]\n");
        }
        if( $x >= $this->width ){
            throw new Exception( "getCell(" . $x . "," . $y . ") " . $x . " off grid. [" . $this->width . "," . $this->height. "]\n");
        }
        return $this->grid[$y][$x];
    }

    /**
     * setCell : Set the contents of a cell. Error if it's out of bounds.
     * @param int $x
     * @param int $y
     * @param GridCell $symbol
     * 
     * @return void
     * @throws Exception
     */
    public function setCell( int $x, int $y, GridCell $symbol )
    {
        if( $y >= $this->height ){
            throw new Exception( "setCell(" . $x . "," . $y . ") " . $y . " off grid. [" . $this->width . "," . $this->height. "]\n");
        }
        if( $x >= $this->width ){
            throw new Exception( "setCell(" . $x . "," . $y . ") " . $x . " off grid. [" . $this->width . "," . $this->height. "]\n");
        }
        $this->grid[$y][$x] = $symbol;
    }

    /**
     * print : Draw the current grid to the console starting at a given column (default 0)
     * @param int $startX
     * 
     * @return void
     */
    public function print(int $startX = 0)
    {
        for( $y = 0; $y < $this->height; $y++ ) {
            for( $x = $startX; $x < $this->width; $x++) {
                switch( $this->grid[$y][$x]) {
                    case GridCell::Empty:
                        echo ".";
                        break;
                    case GridCell::Rock:
                        echo "#";
                        break;
                    case GridCell::Sand:
                        echo "o";
                        break;
                }
            }
            echo "\n";
        }
    }
}

/**
 * Manage drawing rocks to the grid
 * class: RockLine
 */
class RockLine
{
    private Grid $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;    
    }

    /**
     * addRocks : Draw a line of rocks in the grid
     * @param string $from
     * @param string $to
     * 
     * @return void
     * @throws Exception
     */
    public function addRocks( string $from, string $to ): void
    {
        list($xc,$yc) = explode(",", trim($from));
        $x1 = (int)$xc;
        $y1 = (int)$yc;
        list($xc,$yc) = explode(",", trim($to));
        $x2 = (int)$xc;
        $y2 = (int)$yc;

        // Vertical
        if( $x1 == $x2 ){
            for( $y = min($y1,$y2); $y <= max($y1,$y2); $y++) {
                $this->grid->setCell( $x1, $y, GridCell::Rock );
            }
            return;
        }
        // Horizontal
        if( $y1 == $y2 ) {
            for( $x = min($x1,$x2); $x <= max($x1,$x2); $x++) {
                $this->grid->setCell($x, $y1, GridCell::Rock );
            }
            return;
        }
        // Diagonal
        throw new Exception( "Diagonal line detected [" . $x1 . "," . $y1 . "->" . $x2 . "," . $y2 . "]" );
    }
    
    /**
     * Set the bottom row of the grid to be all rocks
     * 
     * @return void
     */
    public function addFloor(): void
    {
        $y = $this->grid->getHeight() - 1;

        for( $x = 0; $x < $this->grid->getWidth(); $x++ ){
            $this->grid->setCell($x, $y, GridCell::Rock);
        }
    }

}

/**
 * Class to simulate dropping sand the size of boulders
 * class: Sand
 */
class Sand
{
    private Grid $grid;
    private int $startX;
    private int $startY;

    public function __construct(Grid $grid, int $x = 500, int $y = 0 )
    {
        $this->grid = $grid;
        $this->startX = $x;
        $this->startY = $y;
    }

    /**
     * Drop the sand
    */
    public function drop() : bool
    {
        $x = $this->startX;
        $y = $this->startY;
        $dropping = true;
        while( $dropping ){
            // We're falling off the bottom so part 1 is done
            if( $y == $this->grid->getHeight() - 1 ){
                return true;
            }
            // Drop while there is space below us
            while( $this->grid->getCell($x, $y+1) == GridCell::Empty) {
                $y++;
                // We're falling off the bottom so part 1 is done
                if( $y == $this->grid->getHeight() - 1 ){
                    return true;
                }
            }
            // We've ether hit sand or rock so check if we can drop to the left or right. If not we've landed.
            if( $this->grid->getCell($x-1, $y+1) == GridCell::Empty ) {
                $x--;
                $y++;
            } else if( $this->grid->getCell($x+1, $y+1) == GridCell::Empty ) {
                $x++;
                $y++;
            } else {
                $dropping = false;
            }
        }
        // Draw the sand on the grid
        $this->grid->setCell($x, $y, GridCell::Sand);
        // If we haven't dropped at all, then part 2 is done.
        if(($x == $this->startX) && ($y == $this->startY)) {
            return true;
        }
        return false;
    }
}

class Controller
{
    private MinMax $minmax;
    private RockLine $rockLine;
    private Grid $grid;

    /**
     * __construct : Build objects
     */
    public function __construct()
    {
        $this->minmax = new MinMax();
        $this->grid = new Grid();
        $this->rockLine = new RockLine($this->grid);
    }

    /**
     * minMax : Given a string of coordinates separated by ' -> ', Update the min and max coordinate values
     * @param mixed $str
     * 
     * @return void
     */
    private function minMax($str): void
    {
        for( $iter = new ArrayIterator(explode(' -> ', $str)); $iter->valid(); $iter->next()) {
            $this->minmax->update($iter->current());
        }
    }

    /**
     * addRocks : Add the lines created by a string of coordinates into the grid as rocks
     * @param mixed $str
     * 
     * @return void
     */
    private function addRocks($str): void
    {
        $iter = new ArrayIterator(explode(' -> ', $str));
        $start = $iter->current();
        $iter->next();
        while( $iter->valid()) {
            $end = $iter->current();
            $this->rockLine->addRocks($start, $end);
            $start = $end;
            $iter->next();
        }
    }

    /**
     * buildGrid : Create a grid and fill it with rocks from the lines specified in a file.
     * @param mixed $filename
     * 
     * @return void
     * @throws Exception
     */
    public function buildGrid($filename)
    {
        /*
         * Pass 1. Get the minmax of the rock coordinates so we know how big to make the grid
         */
        // Start with the sand drop coordinate
        $this->minmax->update("500,0");
        $file = new SplFileObject($filename, "r");
        while( !$file->eof()) {
            $line = trim($file->fgets());
            if( strlen($line) > 0 ){
                $this->minMax($line);
            }
        }
        $file = null;

        /*
         * Pass 2. Create the empty grid then put the rocks in it.
         */
        $this->grid->allocate( $this->minmax->maxX + 1, $this->minmax->maxY);
        $file = new SplFileObject($filename, "r");
        while( !$file->eof()) {
            $line = trim($file->fgets());
            if( strlen($line) > 0 ){
                $this->addRocks($line);
            }
        }
    }

    /**
     * addFloor : Extend the grid to the width it needs to be to fill the grid to the top then add 2 rows to put a floor in.
     * 
     * @return void
     * @throws Exception
     */
    public function addFloor(): void
    {
        $dx = (503 + $this->grid->getHeight()) - $this->grid->getWidth();
        $dy = 2;
        $this->grid->extend($dx, $dy);
        $this->rockLine->addFloor();
    }

    /**
     * dropSand : Create a sand object and drop it
     * @param int $dropped      Part2 continues from part1 so this is how much sand has already dropped to save restarting
     * 
     * @return int              How much sand has dropped.
     * @throws Exception
     */
    public function dropSand(int $dropped = 0): int
    {
        $grains = $dropped;
        // Continue dropping sand until it falls off the bottom (Part1) or fills up (Part2)
        do {
            $sand = new Sand($this->grid);
            $grains++;
        } while(!$sand->drop());
        // $this->grid->print(500 - $this->grid->getHeight());
        return $grains - 1;
    }
}

$controller = new Controller();
$controller->buildGrid("day14input.txt");
$grains = $controller->dropSand();
echo "\nPart 1: " . $grains . " sand dropped.\n\n";
$controller->addFloor();
echo "\nPart 2: " . $controller->dropSand($grains) + 1 . " sand dropped.\n";

