<?php
declare(strict_types = 1);

/**
 * Coordinate class
 */
class Coord
{
    public readonly int $row;
    public readonly int $col;
    public readonly mixed $previous;
    public int $priority;

    /**
     * __construct : previous Points to the Coordinate that created this one or null if this is the start. That way we can walk
     * back through the route that got us here.
     * @param int $row
     * @param int $col
     * @param mixed $previous  
     * 
     * @return void
     */
    public function __construct(int $row, int $col, mixed $previous = null)
    {
        $this->row = $row;
        $this->col = $col;
        $this->previous = $previous;
        $this->priority = 0;                // Priority is so we can prioritise coordinates that are going upwards
    }

    /**
     * offset : Create a new coordinate from this one
     * @param int $offRow
     * @param int $offCol
     * 
     * @return Coord
     */
    public function offset(int $offRow, int $offCol): Coord
    {
        return new Coord( $this->row + $offRow, $this->col + $offCol, $this );
    }

    /**
     * __toString : Debug
     * 
     * @return string
     */
    public function __toString(): string
    {
        return "(" . $this->row . "," . $this->col . ")";
    }

    /**
     * equals : Check if this coordinate is the same as another
     * @param Coord $that
     * 
     * @return bool
     */
    public function equals( Coord $that ): bool
    {
        return (($this->row == $that->row) && ($this->col == $that->col));
    }
}

/**
 * Create a queue that prioritises coordinates by height
 * class: CoordinateQueue
 */
class CoordinateQueue extends SplPriorityQueue
{
    public function compare(mixed $priority1, mixed $priority2): int {
        return $priority1 <=> $priority2;
    }
}

/**
 * Find a path on a map
 * class: PathFinder
 */
class PathFinder
{
    private $map;
    private $discovered;
    
    public function __construct(array $map)
    {
        $this->map = $map;
        $this->discovered = array();
        foreach($map as $row) {
            $this->discovered[] = array_fill(0, strlen($row), 0);
        }
    }

    /**
     * isAccessible : Check if we can get from one node to another. We can jump down but can only climb 1 level
     * @param Coord $from
     * @param Coord $coord
     * 
     * @return bool
     */
    public function isAccessible( Coord $from, Coord &$coord): bool
    {
        if ($coord->row < 0 || $coord->row > count($this->map) - 1) return false;
        if ($coord->col < 0 || $coord->col > strlen($this->map[0]) - 1) return false;
        if ($this->discovered[$coord->row][$coord->col] == 1 ) return false;
        $ordFrom = ord(substr($this->map[$from->row], $from->col, 1));
        $ordCoord = ord(substr($this->map[$coord->row], $coord->col, 1));
        if ($ordCoord > $ordFrom+1) return false;
        $coord->priority = $ordCoord - $ordFrom;
        return true;
    }

    /**
     * discover : Mark a coordinate as discovered
     * @param Coord $coord
     * 
     * @return void
     */
    public function discover( Coord $coord ): void {
        $this->discovered[$coord->row][$coord->col] = 1;
    }

    /**
     * findNeighbours : Find all the accessible neighbours from a coordinate
     * @param Coord $coord
     * 
     * @return CoordinateQueue          Accessible neighbours prioritised by height.
     */
    public function findNeighbours(Coord $coord): CoordinateQueue {
        $neighbours = new CoordinateQueue();
        $up = $coord->offset(-1,  0);
        $down = $coord->offset(1,  0);
        $left = $coord->offset(0, -1);
        $right = $coord->offset(0, 1);
        if ($this->isAccessible($coord, $up)) $neighbours->insert( $up, $up->priority);
        if ($this->isAccessible($coord, $down)) $neighbours->insert( $down, $down->priority);
        if ($this->isAccessible($coord, $left)) $neighbours->insert( $left, $left->priority);
        if ($this->isAccessible($coord, $right)) $neighbours->insert( $right, $right->priority);
        return $neighbours;
    }

    /**
     * findPath : Find a path
     * @param Coord $start
     * @param Coord $end
     * 
     * @return array|null
     */
    public function findPath(Coord $start, Coord $end): array | null {
        $finished = false;
        $used = [];
        $used[] = $start;
        $this->discover($start);
        while (!$finished) {
            $newOpen = [];
            for($i = 0; $i < count($used); $i++){
                $coord = $used[$i];
                $neighbours = $this->findNeighbours($coord);
                while( !$neighbours->isEmpty() ){
                    $neighbour = $neighbours->extract();
                    $isUsed = false;
                    foreach( $newOpen as $test ) {
                        if( $test->equals($neighbour)) {
                            $isUsed = true;
                            break;
                        }
                    }
                    if (!$isUsed) {
                        $newOpen[] = $neighbour;
                    }
                }
            }

            if (count($newOpen) == 0) {
                return null;
            }

            foreach ($newOpen as $coord) {
                $used[] = $coord;
                $this->discover($coord);
                if ($end->equals($coord)) {
                    $finished = true;
                    break;
                }
            }

        }

        $path = [];
        $coord = $used[ count($used) - 1 ];
        while($coord->previous != null) {
            $path[] = $coord;
            $coord = $coord->previous;
        }
        $path[] = $start;
        return $path;
    }
}

/**
 * findStart : Find the start position and replace it with an 'a'
 * @param array $map
 * 
 * @return Coord
 */
function findStart( array &$map ): Coord
{
    $r = 0;
    $c = 0;
    foreach( $map as $row ){
        $c = strpos($row, "S");
        if($c !== false) {
            break;
        }
        $r++;
    }
    $map[$r] = str_replace("S", "a", $map[$r]);
    return new Coord($r, $c);
}

/**
 * findEnd : Find the end position and replace it with a 'z'
 * @param array $map
 * 
 * @return Coord
 */
function findEnd( array &$map ): Coord
{
    $r = 0;
    $c = 0;
    foreach( $map as $row ){
        $c = strpos($row, "E");
        if($c !== false) {
            break;
        }
        $r++;
    }
    $map[$r] = str_replace("E", "z", $map[$r]);
    return new Coord($r, $c);
}

/**
 * loadMap : Read the map from a file
 * @param mixed $filename
 * 
 * @return array
 */
function loadMap($filename): array {
    $file = new SplFileObject($filename, "r");
    $map = [];
    while( !$file->eof()) {
        $line = trim($file->fgets());
        if( strlen($line)  > 0 ) {
            $map[] = $line;
        }
    }
    return $map;
}

$map = loadMap( "day12input.txt" );
$start = findStart($map);
$end = findEnd($map);

$pathFinder = new PathFinder($map);
$path = $pathFinder->findPath( $start, $end );
if ($path != null) {
    echo "Part 1: " . count($path) - 1 . " Steps\n";
} else {
    echo "No path found.\n";   
}

$minpath = 5000;
for($row = 0; $row < 41; $row++) {
    $pathFinder = new PathFinder($map);
    $start = new Coord($row, 0);
    $path = $pathFinder->findPath( $start, $end );
    if($path !== null) {
        echo count($path) - 1 . ",";
        if( (count($path) - 1) < $minpath ){
            $minpath = count($path) - 1;
        }
    }
}
echo "\nPart 2: " . $minpath . " Steps\n";
