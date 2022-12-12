<?php
declare(strict_types = 1);

/**
 * This is bloody horrible code :D
 */
class Monkey
{
    private SplQueue $levels;
    private int $inspections;
    private int $sanityModulo;
    private string $operation;
    private int $opConst;
    private int $divisor;
    private int $trueTarget;
    private int $falseTarget;

    public function __construct( array $levels, string $operation, int $const, int $divisor, int $trueTarget, int $falseTarget ) {
        $this->levels = new SplQueue();
        $this->queueLevels($levels);
        $this->inspections = 0;
        $this->sanityModulo = 1;
        $this->operation = $operation;
        $this->opConst = $const;
        $this->divisor = $divisor;
        $this->trueTarget = $trueTarget;
        $this->falseTarget = $falseTarget;
    }

    public function getDivisor() {
        return $this->divisor;
    }

    public function setSanityModulo( $modval ) {
        $this->sanityModulo = $modval;
    }

    public function queueLevels( array $levels ): void
    {
        foreach($levels as $level) {
            $this->addLevel((int)$level);
        }
    }

    public function addLevel( int $level ): void
    {
        $this->levels->enqueue($level);
    }

    public function getInspections(): int
    {
        return $this->inspections;
    }

    public function takeTurn(array $monkeys, bool $getsBored = true): void
    {
        while( !$this->levels->isEmpty() ){
            $level = (int)$this->levels->dequeue();
            $level = $this->inspectItem($level);
            $this->inspections++;
            if( $getsBored) {
                $level = (int)floor($level / 3);
            }
            $this->throwItem($level, $monkeys);
        }
    }

    protected function inspectItem(int $level): int
    {
        $result = 0;
        switch( $this->operation ){
            case 'square':
                $result = $level * $level;
                break;
            case 'add':
                $result = $level + $this->opConst;
                break;
            case 'mult':
                $result = $level * $this->opConst;
                break;
            default:
                throw new Exception("Unknown operation " . $this->operation);
        }
        return $result;
    }

    protected function throwItem(int $level, array $monkeys): void
    {
        $level = $level % $this->sanityModulo;

        if( ($level % $this->divisor) == 0 ) {
            $monkeys[$this->trueTarget]->addLevel($level);
        } else {
            $monkeys[$this->falseTarget]->addLevel($level);
        }
    }

    public function __toString()
    {
        $out = "[";
        $count = $this->levels->count();
        if( $count > 0 ) {
            for( $i = 0; $i < ($count-1); $i++ ) {
                $out .= $this->levels[$i] . ", ";
            }
            $out .= $this->levels[$count-1];
         }
        return $out . "]";
    }
}

class Controller
{
    private array $monkeys;

    public function __construct()
    {
        $this->monkeys = array();
        $this->monkeys[0] = new Monkey( [71, 56, 50, 73],                 'mult',  11, 13, 1, 7 );
        $this->monkeys[1] = new Monkey( [70, 89, 82],                     'add',    1,  7, 3 ,6 );
        $this->monkeys[2] = new Monkey( [52, 95],                         'square', 0,  3, 5, 4 );
        $this->monkeys[3] = new Monkey( [94, 64, 69, 87, 70],             'add',    2, 19, 2, 6 );       
        $this->monkeys[4] = new Monkey( [98, 72, 98, 53, 97, 51],         'add',    6,  5, 0, 5 );       
        $this->monkeys[5] = new Monkey( [79],                             'add',    7,  2, 7, 0 );       
        $this->monkeys[6] = new Monkey( [77, 55, 63, 93, 66, 90, 88, 71], 'mult',   7, 11, 2, 4 );       
        $this->monkeys[7] = new Monkey( [54, 97, 87, 70, 59, 82, 59],     'add',    8, 17, 1, 3 ); 
        
        $modulo = $this->monkeys[0]->getDivisor();
        for( $m = 1; $m < 8; $m++) {
            $modulo *= $this->monkeys[$m]->getDivisor();
        }
        for( $m = 0; $m < 8; $m++) {
            $this->monkeys[$m]->setSanityModulo($modulo);
        }
    }

    public function takeTurn(bool $getsBored = true) {
        for($m = 0; $m < 8; $m++) {
            $this->monkeys[$m]->takeTurn($this->monkeys, $getsBored );
        }
    }

    public function part1() {
        for($t = 0; $t < 20; $t++) {
            $this->takeTurn(true);
        }
        echo "Part 1:\n";
        $inspections = new SplMaxHeap();
        for( $m = 0; $m < 8; $m++) {
            $inspections->insert( $this->monkeys[$m]->getInspections() );
        }
        echo "2 highest -> " . $inspections->extract() * $inspections->extract() . "\n";
    }

    public function part2() {
        for($t = 0; $t < 10000; $t++) {
            $this->takeTurn(false);
        }
        echo "Part 2:\n";
        $inspections = new SplMaxHeap();
        for( $m = 0; $m < 8; $m++) {
            $inspections->insert( $this->monkeys[$m]->getInspections() );
        }
        echo "2 highest -> " . $inspections->extract() * $inspections->extract() . "\n";
    }
}


$controller = new Controller();
$controller->part1();
$controller->part2();
