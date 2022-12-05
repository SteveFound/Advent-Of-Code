<?php
declare(strict_types = 1);

class Elf {
    public readonly int $min;
    public readonly int $max;

    public function __construct($definition) {
        $range = explode('-', $definition);
        $this->min = (int)$range[0];
        $this->max = (int)$range[1];
    }

    public function encloses(Elf $that): bool {
        return (($this->min <= $that->min) && ($this->max >= $that->max));
    }

    public function overlaps(Elf $that): bool {
        return (($this->min <= $that->max) && ($this->max >= $that->min));
    }
}

$totalPart1 = 0;
$totalPart2 = 0;
$file = new SplFileObject("day4input.txt", "r");
while( !$file->eof()) {
    $line = trim($file->fgets());
    if( strlen($line)  > 0 ) {
        $ranges = explode(',', $line);
        $elf1 = new Elf($ranges[0]);
        $elf2 = new Elf($ranges[1]);

        $totalPart1 += (($elf1->encloses($elf2)) || ($elf2->encloses($elf1))) ? 1 : 0;
        $totalPart2 += ($elf1->overlaps($elf2)) ? 1 : 0;
    }
}
echo "Part 1 total : " . $totalPart1 . "\n";
echo "Part 2 total : " . $totalPart2 . "\n";