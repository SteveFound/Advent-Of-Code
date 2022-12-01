<?php
declare(strict_types = 1);

/**
 * parseFile : Parse data file into an array
 * @param string $filename
 * 
 * @return array
 */
function parseFile($filename): array
{
    $totals = array();

    $idx = 0;
    $sumValues = 0;
    $file = new SplFileObject($filename, "r");
    if( !$file->eof() ) {
        do {
            $line = trim($file->fgets());
            if( strlen($line) > 0 ) {
                $sumValues += (int)$line;
            } else {
                $totals[$idx] = $sumValues;
                $sumValues = 0;
                $idx++;
            }
        } while (!$file->eof());
    }
    if( $sumValues > 0) {
        $totals[$idx] = $sumValues;
    }

    return $totals;
}

// Read the datafile into an array
$elfTotals = parseFile("input.txt");

// Reverse sort the array so highest value is in element 0 (Part 1)
rsort($elfTotals);
// Sum the highest 3 values (Part 2)
$gtotal = $elfTotals[0] + $elfTotals[1] + $elfTotals[2];

echo "Part 1\n";
echo "Most Calories : " . $elfTotals[0] . "\n";

echo "\n\nPart 2\n";
echo "Sum of 3 highest Calories : " . $gtotal . "\n";
?> 