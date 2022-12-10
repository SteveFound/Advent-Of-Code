<?php
declare(strict_types = 1);

/**
 * The processor class which manages register X
 * class: Processor
 */
class Processor {
    private array $regX;
    private int $currValue;

    public function __construct()
    {
        $this->regX = array();
        $this->currValue = 1;
    } 

    /**
     * noop : Takes one cycle and X maintains the same value for that cycle.
     * 
     * @return void
     */
    public function noop(): void
    {
        $this->regX[] = $this->currValue;
    }

    /**
     * addX : Takes 2 cycles then X is modified at the start of the following cycle
     * @param int $value
     * 
     * @return void
     */
    public function addX(int $value): void
    {
        $this->regX[] = $this->currValue;
        $this->regX[] = $this->currValue;
        $this->currValue += $value;
    }

    /**
     * getX : Get the value of X for a given cycle
     * @param int $cycle
     * 
     * @return int
     */
    public function getX(int $cycle): int
    {
        return $this->regX[$cycle-1];
    }

    /**
     * getSignal : Get the signal strength for a given cycle
     * @param int $cycle
     * 
     * @return int
     */
    public function getSignal(int $cycle): int
    {
        return $cycle * $this->getX($cycle);
    }

    /**
     * __toString : convert the cycle array to a string
     * 
     * @return string
     */
    public function __toString(): string
    {
        $out = '';
        for( $cycle = 1; $cycle <= count($this->regX); $cycle++ ) {
            $out .= $cycle . " : " . $this->getX($cycle) . " [" . $this->getSignal($cycle) . "]\n";
        }
        $out .= "X : " . $this->currValue . "\n";
        return $out;
    }
}

class Controller {
    private Processor $proc;

    public function __construct()
    {
        $this->proc = new Processor();
    }

    public function execute( string $cmd )
    {
        if( strlen($cmd) == 0 ) return;

        $parts = explode(' ', $cmd);
        switch( $parts[0]) {
            case 'noop':
                $this->proc->noop();
                break;
            case 'addx':
                $this->proc->addX((int)$parts[1]);
        }
    }

    public function sumCycles(array $cycles): int
    {
        $total = 0;
        foreach($cycles as $cycle){
            $total += $this->proc->getSignal($cycle);
        }
        return $total;
    }

    public function drawImage(): void
    {
        $cycle = 1;
        for( $row = 0; $row < 6; $row++ ) {
            for( $col = 0; $col < 40; $col++ ) {
                $spriteX = $this->proc->getX($cycle);
                if( ($col >= $spriteX - 1) && ($col <= $spriteX + 1) ) {
                    echo '#';
                } else {
                    echo '.';
                }
                $cycle++;
            }
            echo "\n";
        }
    }

    public function debug()
    {
        echo $this->proc;
    }
}

$controller = new Controller();
$file = new SplFileObject("day10input.txt", "r");
while( !$file->eof()) {
    $controller->execute( trim($file->fgets()) );
}

echo "Part 1 : Sum of signals " . $controller->sumCycles([20,60,100,140,180,220]);
echo "\n\nPart 2:\n";
$controller->drawImage();
?>