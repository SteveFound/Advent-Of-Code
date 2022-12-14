<?php
declare(strict_types = 1);

/**
 * parseInput : Convert the text input into an actual array. The string is very conveniantly in PHP array format
 * @param string $line
 * 
 * @return array
 */
function parseInput( string $line ): array
{
    $out = [];
    eval("\$out =" . $line . ";");
    return $out;
}

/**
 * compare : Recursive comparator function to handle comparing mixtures of integers and arrays
 * @param mixed $left
 * @param mixed $right
 * 
 * @return int
 * @throws Exception
 */
function compare( mixed $left, mixed $right ): int
{
    if( is_int($left)) {
        // Left and right are both integers. We return 1 if left < right, 0 if left == right and -1 if left > right 
        if( is_int($right) ){
            return $right <=> $left;
        // Left is integer, Right is array so convert left to an array and recurse
    } elseif ( $right instanceof ArrayIterator) {
            return compare( new ArrayIterator([$left]), $right );
        }
        // Left is an integer, Right is something wierd.
        throw new Exception( "Don't know what to do..." );
    }
    if( $left instanceof ArrayIterator ) {
        // Left is array, Right is integer so convert right to an array and recurse
        if( is_int($right) ){
            return compare( $left, new ArrayIterator([$right]));
        // Left and Right are both arrays
        } elseif ( $right instanceof ArrayIterator) {
            do {
                // If we have reached the end of both arrays then they are equal
                if( !$left->valid() && !$right->valid()) {
                    return 0;
                // If we have reached the end of the left array but not the right, then left < right which is good
                } else if( !$left->valid()) {
                    return 1;
                // If we have reached the end of the right array but not the left, then left > right which is bad
                } else if( !$right->valid()) {
                    return -1;
                }
                // if the next item is an embedded array, it needs to be an iterator
                if( is_array($left->current())){
                    $l = new ArrayIterator($left->current());
                // Next item must be an int... hopefully
                } else {
                    $l = $left->current();
                }
                if( is_array($right->current())){
                    $r = new ArrayIterator($right->current());
                } else {
                    $r = $right->current();
                }
                // recurse to compare the new items
                $cmp = compare( $l, $r );
                // Move to the next elements
                $left->next();
                $right->next();
                // Only continue if those items were equal
            } while ($cmp == 0);
            return $cmp;
        }
        throw new Exception( "Don't know what to do..." );
    }
    throw new Exception( "Don't know what to do..." );
    return 0;
}

// Read the array definitions as strings for part2
$file = new SplFileObject("day13input.txt", "r");
$textArrays = [];
while( !$file->eof())
{
    $line = trim($file->fgets());
    if( strlen($line) > 0){
        $textArrays[] = $line;
    }
}


$pair = 1;          // Pair index
$part1 = 0;         // Running total

// Let's play wih PHP iterators
$inputIterator = new ArrayIterator($textArrays);
for( $inputIterator->rewind(); $inputIterator->valid(); $inputIterator->next())
{
    $left = new ArrayIterator(parseInput(trim($inputIterator->current())));
    $inputIterator->next();
    $right = new ArrayIterator(parseInput(trim($inputIterator->current())));
    $result = compare( $left, $right );
    if( $result == 1) {
        $part1 += $pair;
    }
    $pair++;
}
$file = null;
echo "Part1: " . $part1 . "\n";

/** Part2 ... add the two keys to the input */
$textArrays[] = '[[2]]';
$textArrays[] = '[[6]]';

// Sort the array using our compare function. usort returns the 2nd item first for some reason
usort($textArrays, function($right, $left) {
    return compare(new ArrayIterator(parseInput($left)), new ArrayIterator(parseInput($right)));
});

// The reason the input was left as strings is so it could easily be searched here
echo "Part2: " . (array_search('[[2]]', $textArrays) + 1) * (array_search('[[6]]', $textArrays) + 1);



