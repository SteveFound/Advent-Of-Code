<?php
declare(strict_types = 1);

use MinMax as GlobalMinMax;

/**
 * Manage a sensor. 
 * class: Sensor
 */
class Sensor
{
    private int $beaconX;
    private int $beaconY;

    /*
     * The PHP 8.1 readonly attribute makes it safe to declare these properties as public. They can only be set once by the object
     * and then become constants.
     */
    public readonly int $x;
    public readonly int $y;
    public readonly int $range;
    public readonly int $minX;
    public readonly int $minY;
    public readonly int $maxX;
    public readonly int $maxY;

    public function __construct(int $x, int $y, int $bx, int $by)
    {
        $this->x = $x;
        $this->y = $y;
        $this->beaconX = $bx;
        $this->beaconY = $by;
        $this->range = $this->manhattanDistance($bx, $by);
        $this->minX = $x - $this->range;
        $this->minY = $y - $this->range;
        $this->maxX = $x + $this->range;
        $this->maxY = $y + $this->range;
    }

    /**
     * manhattanDistance : Get the manhattan distance from the sensor to another point
     * @param int $targetX
     * @param int $targetY
     * 
     * @return int
     */
    private function manhattanDistance( int $targetX, int $targetY ): int
    {
        return (int)(abs($this->x - $targetX) + abs($this->y - $targetY));
    }


    /**
     * inRange : Check if the distance between a point and the sensor is within the range of it's beacon
     * @param int $targetX
     * @param int $targetY
     * 
     * @return bool
     */
    public function inRange(int $targetX, int $targetY, bool $excludeBeacon ): bool
    {
        // If the coordinate is a beacon, it does not count
        if( $excludeBeacon && ( $this->beaconX == $targetX ) && ( $this->beaconY == $targetY )) {
            return false;
        }
        return ($this->manhattanDistance($targetX, $targetY) <= $this->range);
    }

    /**
     * ManhattanX : Given a Y coordinate, calculate the X coordinate where a point leaves scanner range
     * @param int $y
     * 
     * @return int|bool
     */
    public function manhattanMaxX( int $y ) : int|bool
    {
        // given that M = abs(x - sx) + abs(y - sy) where sx and sy are the coordinates of the scanner
        // x = M - abs(y - sy) + sx
        if( $this->intersects($y)) {
            return $this->range - abs($y - $this->y) + $this->x;
        }
        return false;
    }

    /**
     * intersects : Check if a line intersects this sensor
     * @param mixed $y
     * 
     * @return bool
     */
    public function intersects( $y ): bool
    {
        return (($y >= $this->minY) && ($y <= $this->maxY));
    }

    /**
     * __toString : Convert to a string
     * 
     * @return string
     */
    public function __toString()
    {
        return "Sensor [X:" . $this->x . ",y:" . $this->y . ",r:" . $this->range . "]";
    }
}

/**
 * Class to find the minimum and maximum range covered by an array of Sensors
 * class: MinMax
 */
class MinMax
{
    public readonly int $minX;
    public readonly int $maxX;
    public readonly int $minY;
    public readonly int $maxY;

    public function __construct( array $sensors ){
        $minx = PHP_INT_MAX;
        $miny = PHP_INT_MAX;
        $maxx = PHP_INT_MIN;
        $maxy = PHP_INT_MIN;
        foreach( $sensors as $sensor ){
            $minx = min($minx, $sensor->minX);
            $miny = min($miny, $sensor->minY);
            $maxx = max($maxx, $sensor->maxX);
            $maxy = max($maxy, $sensor->maxY);
        }
        $this->minX = $minx;
        $this->minY = $miny;
        $this->maxX = $maxx;
        $this->maxY = $maxy;
    }

    public function __toString()
    {
        return "MinMax: [(" . $this->minX . "," . $this->minY . "), (" . $this->maxX . "," . $this->maxY . ")]";
    }
}

/**
 * class: Controller
 */
class Controller
{
    private array $sensors;

    public function __construct()
    {
        $this->sensors = [];
    }

    /**
     * parseFile : Read and sort the sensors
     * @param mixed $filename
     * 
     * @return void
     */
    private function parseFile($filename)
    {
        $file = new SplFileObject($filename, "r");
        while( !$file->eof()) {
            $line = trim($file->fgets());
            if( strlen($line) > 0 ){
                list($sx, $sy, $bx, $by) = sscanf($line, "Sensor at x=%d, y=%d: closest beacon is at x=%d, y=%d");
                $this->sensors[] = new Sensor($sx, $sy, $bx, $by);
            }
        }

        // Sort sensors by Y then X
        uasort($this->sensors, function(Sensor $a, Sensor $b) {
            if( $a->y == $b->y) {
                return $a->x <=> $b->x;
            } else {
                return $a->y <=> $b->y;
            }
        });
    }

    /**
     * testSensors : Count how many cells on a given line are covered by sensors
     * @param int $y
     * 
     * @return int
     */
    private function testSensors(int $y): int
    {
        // Find the min and max X covered by the remaining sensors
        $minmax = new MinMax($this->sensors);
        $iter = new ArrayIterator($this->sensors);

        $covered = 0;
        for( $x = $minmax->minX; $x <= $minmax->maxX; $x++) {
            for( $iter->rewind(); $iter->valid(); $iter->next() ){
                if(($iter->current()->intersects($y)) && ($iter->current()->inRange($x, $y, false))){
                    $newX = $iter->current()->manhattanMaxX($y);
                    $covered += ($newX - $x) + 1;
                    $x = $newX;
                    break;
                }
            }
        }
        return $covered - 1;
    }

    /**
     * findUncoveredSpaces : Find any positions that are not covered by sensors
     */
    private function findUncoveredSpaces(): void
    {
        $minmax = new MinMax($this->sensors);
        $ystart = max(0, $minmax->minY);
        $yend = min(4000000, $minmax->maxY);
        $xstart = max(0, $minmax->minX);
        $xend = min(4000000, $minmax->maxX);

        $iter = new ArrayIterator($this->sensors);
        for ($y = $ystart; $y < $yend; $y++) {
            for ($x = $xstart; $x < $xend; $x++ ){
                for( $iter->rewind(); $iter->valid(); $iter->next() ){
                    if( $iter->current()->inRange($x, $y, false) ){
                        $x = $iter->current()->manhattanMaxX($y);
                        break;
                    }
                }
                if( !$iter->valid() ){
                    echo "\nCoordinate " . $x . "," . $y . " Is not covered by a sensor. Frequency: " . ($x * 4000000) + $y. "\n";
                }
            }
        }
    }

    public function part1(): int {
        $this->parseFile("day15input.txt");
        return $this->testSensors(2000000);
    }

    public function part2(): void {
        $this->findUncoveredSpaces();
    }

}

$controller = new Controller();
echo "Part 1:" . $controller->part1() . "\n";
echo "Part 2:\n";
$controller->part2() . "\n";
