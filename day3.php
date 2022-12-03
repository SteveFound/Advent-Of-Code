<?php
declare(strict_types = 1);

$file = new SplFileObject("day3input.txt", "r");
$totalPart1 = 0;
while( !$file->eof()) {
    $line = trim($file->fgets());
    if( strlen($line) > 0) {
        // Split string in half
        $halves = str_split( $line, strlen($line) / 2);
        // Walk through each character of first half
        foreach (str_split($halves[0]) as $chr) {
            // if character exists in both strings, we have it.
            if( str_contains( $halves[1], $chr)) {
                // Convert character to it's value
                if( ctype_upper($chr)) {
                    $totalPart1 += (ord($chr) - 38);    // A-Z = 27-52
                } else {
                    $totalPart1 += (ord($chr) - 96);    // a-z = 1 - 26
                }
                break;
            }
        }
    }
}
// Close the file
$file = null;
echo "Part 1\n";
echo "Total Score: " . $totalPart1 . "\n";

/* Part 2 */

$file = new SplFileObject("day3input.txt", "r");
$totalPart2 = 0;
while( !$file->eof()) {
    $line1 = trim($file->fgets());
    if( strlen($line1) > 0) {
        $line2 = trim($file->fgets());
        $line3 = trim($file->fgets());

        // Walk through each character of first line
        foreach (str_split($line1) as $chr) {
            // if character exists in both line 2 and line 3, we have it.
            if( str_contains( $line2, $chr) && str_contains( $line3, $chr)) {
                // Convert character to it's value
                if( ctype_upper($chr)) {
                    $totalPart2 += (ord($chr) - 38);    // A-Z = 27-52
                } else {
                    $totalPart2 += (ord($chr) - 96);    // a-z = 1 - 26
                }
                break;
            }
        }
    }
}

echo "\nPart 2\n";
echo "Total Score: " . $totalPart2 . "\n";
