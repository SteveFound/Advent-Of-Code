<?php
declare(strict_types = 1);

class Grid {
    private array $grid;

    /**
     * __construct : Initialise the grid to an empty array
     * 
     * @return void
     */
    public function __construct() {
        $this->grid = array();
    }

    /**
     * getArray : Return the grid array
     * 
     * @return array
     */
    public function getArray(): array {
        return $this->grid;
    }

    /**
     * height : The height of the grid is the length of the array
     * 
     * @return int
     */
    public function height(): int {
        return count($this->grid);
    }

    /**
     * width : The grid is an array of arrays, so the width of the grid is the length of an array element
     * 
     * @return int
     */
    public function width(): int {
        return count($this->grid[0]);
    }

    /**
     * addRow : Add a row of tree heights to the grid
     * @param array $heights
     * 
     * @return void
     */
    public function addRow(array $heights) {
        $this->grid[] = $heights;
    }

    /**
     * printGrid : Output the grid to the console
     * 
     * @return void
     */
    public function printGrid() {
        foreach( $this->grid as $row ) {
            foreach( $row as $height ) {
                echo $height;
            }
            echo "\n";
        }
    }

    /**
     * visibleLeft : Check if a tree in a given position can be seen from the left
     * @param int $row
     * @param int $col
     * 
     * @return bool
     */
    private function visibleLeft( int $row, int $col ): bool {
        if( $col == 0 ) return true;
        $target = $this->grid[$row][$col];
        // Check left
        $visible = true;
        for( $c = $col-1; $c >= 0; $c-- ) {
            if($this->grid[$row][$c] >= $target) {
                $visible = false;
                break;
            }
        }
        return $visible;
    }

    /**
     * visibleLeft : Check if a tree in a given position can be seen from the right
     * @param int $row
     * @param int $col
     * 
     * @return bool
     */
    private function visibleRight( int $row, int $col ): bool {
        if( $col == $this->width() - 1 ) return true;
        $target = $this->grid[$row][$col];
        // Check right
        $visible = true;
        for( $c = $col+1; $c < $this->width(); $c++ ) {
            if($this->grid[$row][$c] >= $target) {
                $visible = false;
                break;
            }
        }
        return $visible;
    }

    /**
     * visibleLeft : Check if a tree in a given position can be seen from above
     * @param int $row
     * @param int $col
     * 
     * @return bool
     */
    private function visibleTop( int $row, int $col ): bool {
        if( $row == 0 ) return true;
        $target = $this->grid[$row][$col];
        // Check up
        $visible = true;
        for( $r = $row-1; $r >= 0; $r-- ) {
            if($this->grid[$r][$col] >= $target) {
                $visible = false;
                break;
            }
        }
        return $visible;
    }

    /**
     * visibleLeft : Check if a tree in a given position can be seen from below
     * @param int $row
     * @param int $col
     * 
     * @return bool
     */
    private function visibleBottom( int $row, int $col ): bool {
        if( $row == $this->height() ) return true;
        $target = $this->grid[$row][$col];
        // Check down
        $visible = true;
        for( $r = $row+1; $r < $this->height(); $r++ ) {
            if($this->grid[$r][$col] >= $target) {
                $visible = false;
                break;
            }
        }
        return $visible;
    }

    /**
     * isVisible : Check if a tree in a given location can be seen from any direction
     * @param int $row
     * @param int $col
     * 
     * @return bool
     */
    public function isVisible( int $row, int $col ): bool {
        if ( $this->visibleLeft($row, $col) || $this->visibleRight($row, $col) || 
             $this->visibleTop($row, $col) || $this->visibleBottom($row, $col) ) {
                return true;
        }
        return false;
    }

    /**
     * Count how many trees can be seen to a trees west before it's view is blocked
     * @param int $row
     * @param int $col
     * 
     * @return int
     */
    public function scenicLeft( int $row, int $col ): int {
        if( $col == 0 ) return 0;
        $target = $this->grid[$row][$col];
        // Check left
        $scenic = 0;
        for( $c = $col-1; $c >= 0; $c-- ) {
            $scenic++;
            if($this->grid[$row][$c] >= $target) break;
        }
        return $scenic;
    }

    /**
     * Count how many trees can be seen to a trees east before it's view is blocked
     * @param int $row
     * @param int $col
     * 
     * @return int
     */
    public function scenicRight( int $row, int $col ): int {
        if( $col == $this->width() - 1 ) return 0;
        $target = $this->grid[$row][$col];
        // Check right
        $scenic = 0;
        for( $c = $col+1; $c < $this->width(); $c++ ) {
            $scenic++;
            if($this->grid[$row][$c] >= $target) break;
        }
        return $scenic;
    }

    /**
     * Count how many trees can be seen to a trees north before it's view is blocked
     * @param int $row
     * @param int $col
     * 
     * @return int
     */
    public function scenicTop( int $row, int $col ): int {
        if( $row == 0 ) return 0;
        $target = $this->grid[$row][$col];
        // Check up
        $scenic = 0;
        for( $r = $row-1; $r >= 0; $r-- ) {
            $scenic++;
            if($this->grid[$r][$col] >= $target) break;
        }
        return $scenic;
    }

    /**
     * Count how many trees can be seen to a trees south before it's view is blocked
     * @param int $row
     * @param int $col
     * 
     * @return int
     */
    public function scenicBottom( int $row, int $col ): int {
        if( $row == $this->height() - 1 ) return 0;
        $target = $this->grid[$row][$col];
        // Check down
        $scenic = 0;
        for( $r = $row+1; $r < $this->height(); $r++ ) {
            $scenic++;
            if($this->grid[$r][$col] >= $target) break;
        }
        return $scenic;
    }

    /**
     * scenicScore : Calculate the scenic score of the view from a given tree.
     * @param int $row
     * @param int $col
     * 
     * @return int
     */
    public function scenicScore( int $row, int $col ): int {
        return $this->scenicLeft($row, $col) * $this->scenicRight($row, $col) * 
               $this->scenicTop($row, $col) * $this->scenicBottom($row, $col);
    }
}

/**
 * Manage the tree grid.
 * class: GridBuilder
 */
class GridBuilder {
    private Grid $grid;

    /**
     * __construct : We are passed a grid to play with
     * @param Grid $grid
     * 
     * @return void
     */
    public function __construct( Grid $grid) {
        $this->grid = $grid;
    }

    /**
     * addRow : Convert a string of tree heights into an integer array and add that to the grid.
     * @param string $row
     * 
     * @return void
     */
    public function addRow( string $row ) {
        $heights = [];

        $values = str_split($row);
        foreach($values as $height) {
            $heights[] = (int)$height;
        }
        $this->grid->addRow($heights);
    }

    /**
     * part1 : Count how many trees can be seen from any direction.
     * 
     * @return int
     */
    public function part1(): int {
        $visible = 0;
        for($row = 0; $row < $this->grid->height(); $row++ ) {
            for($col = 0; $col < $this->grid->width(); $col++) {
                $visible += $this->grid->isVisible($row, $col) ? 1 : 0;
            }
        }
        return $visible;
    }

    /**
     * part2 : Calculate the best scenic score.
     * 
     * @return int
     */
    public function part2(): int {
        $max = 0;
        for($row = 0; $row < $this->grid->height(); $row++ ) {
            for($col = 0; $col < $this->grid->width(); $col++) {
                $score = $this->grid->scenicScore($row, $col);
                if( $score > $max ) {
                    $max = $score;
                }
            }
        }
        return $max;
    }
}

$gb = new GridBuilder( new Grid() );
$file = new SplFileObject("day8input.txt", "r");
while( !$file->eof()) {
    $gb->addRow( trim($file->fgets()) );
}

echo "\n\nPart 1:\n" .  $gb->part1(). " trees are visible.\n";
echo "Part 2:\n" .  $gb->part2(). " best scenic score.\n";
