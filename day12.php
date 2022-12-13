<?php
declare(strict_types = 1);

class Coord
{
    public readonly int $row;
    public readonly int $col;
    public readonly mixed $previous;
    public int $priority;

    public function __construct(int $row, int $col, mixed $previous = null)
    {
        $this->row = $row;
        $this->col = $col;
        $this->previous = $previous;
        $this->priority = 0;
    }

    public function offset(int $offRow, int $offCol): Coord
    {
        return new Coord( $this->row + $offRow, $this->col + $offCol, $this );
    }

    public function __toString(): string
    {
        return "(" . $this->row . "," . $this->col . ")";
    }

    public function equals( Coord $that ): bool
    {
        return (($this->row == $that->row) && ($this->col == $that->col));
    }
}

class CoordinateQueue extends SplPriorityQueue
{
    public function compare(mixed $priority1, mixed $priority2): int {
        return $priority1 <=> $priority2;
    }
}

class PathFinder
{
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function isAccessible( Coord $from, Coord &$coord): bool
    {
        if ($coord->row < 0 || $coord->row > count($this->map) - 1) return false;
        if ($coord->col < 0 || $coord->col > strlen($this->map[0]) - 1) return false;
        $ordFrom = ord(substr($this->map[$from->row], $from->col, 1));
        $ordCoord = ord(substr($this->map[$coord->row], $coord->col, 1));
        if ( (($ordCoord - $ordFrom) < 0) || (($ordCoord - $ordFrom) > 1)) return false;
        $coord->priority = $ordCoord - $ordFrom;
        return true;
    }

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

    public function findPath(Coord $start, Coord $end): array | null {
        $finished = false;
        $used = [];
        $used[] = $start;
        while (!$finished) {
            $newOpen = [];
            for($i = 0; $i < count($used); $i++){
                $coord = $used[$i];
                // echo "Finding neighbours for coordinate " . $coord;
                $neighbours = $this->findNeighbours($coord);
                while( !$neighbours->isEmpty() ){
                    $neighbour = $neighbours->extract();
                    // echo "Neighbour " . $neighbour;
                    $isUsed = false;
                    foreach( $used as $test ){
                        if( $test->equals($neighbour)) {
                            // echo " already used\n";
                            $isUsed = true;
                            break;
                        }
                    }
                    foreach( $newOpen as $test ) {
                        if( $test->equals($neighbour)) {
                            // echo " already in newOpen[]\n";
                            $isUsed = true;
                            break;
                        }
                    }
                    if (!$isUsed) {
                        // echo "adding to newOpen[]\n";
                        $newOpen[] = $neighbour;
                    }
                }
            }

            if (count($newOpen) == 0) {
                // echo "No new directions found.\n";
                return null;
            }

            foreach ($newOpen as $coord) {
                // echo "Saving " . $coord . " to used[]\n";
                $used[] = $coord;
                if ($end->equals($coord)) {
                    // echo "Found the exit.\n";
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



$map = file( "day12test.txt" );
$start = findStart($map);
$end = findEnd($map);

$pathFinder = new PathFinder($map);
$path = $pathFinder->findPath( $start, $end );
if ($path != null) {
    foreach( $path as $coord) {
        echo $coord . " ";
    }
    echo "\n";
    echo count($path) - 1 . " Steps\n";
} else {
    echo "No path found.\n";   
}