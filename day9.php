<?php
declare(strict_types = 1);

/**
 * The Head and the tails are just grid positions.
 * class: Position
 */
class Position {
    protected int $row;         
    protected int $col;    

    public function __construct(int $row = 0, int $col = 0)
    {
        $this->row = $row;
        $this->col = $col;
    }

    /**
     * rowDistance : Get the distance in rows from this position to another position.
     * @param Position $that
     * 
     * @return int      // +ve $that is below $this, -ve $that is above $this, 0 $that and $this are on the same row
     */
    protected function rowDistance( Position $that ): int {
        return $that->row - $this->row;
    }

    /**
     * colDistance : Get the distance in columns from this position to another position.
     * @param Position $that
     * 
     * @return int      // +ve $that is right of $this, -ve $that is left of $this, 0 $that and $this are in the same column
     */
    protected function colDistance( Position $that ): int {
        return $that->col - $this->col;
    }

    /**
     * distance : Get the absolute distance in rows or columns between this position and another
     * @param Position $that
     * 
     * @return void
     */
    protected function distance( Position $that ): int {
        return max( abs($this->colDistance($that)) , abs($this->rowDistance($that)));
    }

    /**
     * Movement methods
     */
    protected function up(): void    { $this->row--; }
    protected function down(): void  { $this->row++; }
    protected function left(): void  { $this->col--; }
    protected function right(): void { $this->col++; }

    /**
     * __toString : This is implemented so we can use the array_unique() function to filter out unique positions that have 
     * been visited. It turns the object into a string which is just the row and column separated by a comma
     * 
     * @return string
     */
    public function __toString(): string {
        return strval($this->row) . "," . strval($this->col);
    }
}

/**
 * Head is a position that changes from a command
 * class: Head
 */
class Head extends Position {
    public function move( string $direction ): void {
        switch($direction) {
            case "U":
                $this->up();
                break;
            case "D":
                $this->down();
                break;
            case "L":
                $this->left();
                break;
            case "R":
                $this->right();
                break;
        }
    }
}

/**
 * Tail is a position that follows another position
 * class: Tail
 */
class Tail extends Position {
    /**
     * follow : Move towards another position
     * @param Head $head
     * 
     * @return bool         True if this position changed
     */
    public function follow( Position $head ):bool {
        // Nothing to do if in the same square or adjacent
        if($this->distance($head) < 2) {
            return false;
        }

        $rd = $this->rowDistance($head);
        $cd = $this->colDistance($head);
        if( $rd > 0 ) { $this->down(); }
        if( $rd < 0 ) { $this->up(); }
        if( $cd > 0 ) { $this->right(); }
        if( $cd < 0 ) { $this->left(); }
        return true;
    }
}

/**
 * Control the head and the 9 tails.
 * class: Controller
 * 
 * @package 
 * @author Steve Found
 */
class Controller {
    private Head $head;
    private array $tails;
    private array $visitedPart1;
    private array $visitedPart2;

    public function __construct() {
        // The head and 9 tails all start at the same position
        $this->head = new Head(0,0);
        $this->tails = array();
        for($i = 0; $i < 9; $i++) {
            $this->tails[$i] = new Tail(0,0);
        }
        $this->visitedPart1[] = clone($this->tails[0]);
        $this->visitedPart2[] = clone($this->tails[8]);
    }

    /**
     * getVisitedPart1 : Returns the unique nodes visited by tails[0] for part 1
     * 
     * @return array
     */
    public function getVisitedPart1(): array {
        return array_unique($this->visitedPart1);
    }

    /**
     * getVisitedPart2 : Returns the unique nodes visited by tails[8] for part 2
     * 
     * @return array
     */
    public function getVisitedPart2(): array {
        return array_unique($this->visitedPart2);
    }

    /**
     * parse : Execute a command
     * @param string $cmd
     * 
     * @return void
     */
    public function parse(string $cmd) {
        if( strlen($cmd) == 0 ) return;

        $parts = explode(' ', $cmd);
        // Repeat count times
        for($moveCount = 0; $moveCount < (int)$parts[1]; $moveCount++) {
            // Move the head by one cell in the direction specified
            $this->head->move($parts[0]);
            // Tails[0] follows the head and gets logged if it moves
            if( $this->tails[0]->follow($this->head) ) {
                // log a copy of the position otherwise the same position will be logged every time
                $this->visitedPart1[] = clone($this->tails[0]);
            }
            // Tails[1]..[7] follow the previous tail
            for($tailIdx = 1; $tailIdx < 8; $tailIdx++) {
                $this->tails[$tailIdx]->follow($this->tails[$tailIdx-1]);
            }
            // Tail[8] follows tail [7] and gets logged if it moves 
            if( $this->tails[8]->follow($this->tails[7])) {
                $this->visitedPart2[] = clone($this->tails[8]);
            }
/**
 * Debug code which logs all the positions to the console using the __toString function in Position
 *           echo "** " . $cmd . " **\n";
 *           echo "Head:[" . $this->head . "] Tails:[";
 *           for( $tailIdx = 0; $tailIdx < 9; $tailIdx++ ) {
 *               echo $this->tails[$tailIdx] . " ";
 *           }
 *           echo "]\n";
 */
        }
    }
}

$controller = new Controller();
$file = new SplFileObject("day9input.txt", "r");
while( !$file->eof()) {
    $controller->parse( trim($file->fgets()) );
}

$visited = $controller->getVisitedPart1();
echo "\n\nPart 1: Tail visited " . count($controller->getVisitedPart1()) . " grid cells.\n";
echo "Part 2: Tail visited " .  count($controller->getVisitedPart2()). " grid cells.\n";
