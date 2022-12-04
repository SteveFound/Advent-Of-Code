<?php
declare(strict_types = 1);

$totalpart1 = 0;
$totalpart2 = 0;
$file = new SplFileObject("day4input.txt", "r");
while( !$file->eof()) {
    $line = trim($file->fgets());
    if( strlen($line)  > 0 ) {
        // Split the line on the comma to get the two ranges
        $ranges = explode(',', $line);
        // Split the ranges on the '-' to get the min and max for each range
        $minmax1 = explode('-', $ranges[0]);
        $minmax2 = explode('-', $ranges[1]);
        // Get the numbers
        $min1 = (int)$minmax1[0];
        $max1 = (int)$minmax1[1];
        $min2 = (int)$minmax2[0];
        $max2 = (int)$minmax2[1];
        // Check if either range is within the other
        if( (($min1 <= $min2) && ($max1 >= $max2)) || (($min2 <= $min1) && ($max2 >= $max1))) {
                $totalpart1++;
        }
        // Check if either range overlaps the other
        if( ($min1 <= $max2) && ($max1 >= $min2) ) {
                $totalpart2++;
        } 
    }
}
echo "Part 1 total : " . $totalpart1 . "\n";
echo "Part 2 total : " . $totalpart2 . "\n";