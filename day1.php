<?php
declare(strict_types = 1);

/**
 * parseFile : Parse data file into an array
 * @param mixed $handle
 * 
 * @return array
 */
function parseFile($handle): array
{
    $totals = array();

    $idx = 0;
    $sumValues = 0;
    if( $line = fgets($handle) ) {
        do {
            $line = trim($line);
            if( strlen($line) > 0 ) {
                $sumValues += (int)$line;
            } else {
                $totals[$idx] = $sumValues;
                $sumValues = 0;
                $idx++;
            }
        } while (($line = fgets($handle)) !== false);
    }
    if( $sumValues > 0) {
        $totals[$idx] = $sumValues;
    }

    return $totals;
}

// Read the datafile into an array
$handle = fopen("input.txt", "r") or die("Unable to open input file!");
$elfTotals = parseFile($handle);
fclose($handle);

// Reverse sort the array so highest value is in element 0 (Part 1)
rsort($elfTotals);
// Sum the highest 3 values (Part 2)
$gtotal = $elfTotals[0] + $elfTotals[1] + $elfTotals[2];

echo "Part 1\n";
echo "Most Calories : " . $elfTotals[0] . "\n";

echo "\n\nPart 2\n";
echo "Sum of 3 highest Calories : " . $gtotal . "\n";
?> 