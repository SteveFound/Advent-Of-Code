<?php
declare(strict_types = 1);

$file = new SplFileObject("day6input.txt", "r");
while( !$file->eof()) {
    $line = trim($file->fgets());
    /* Walk the string */
    $marker = 0;
    for( $idx = 0; $idx < strlen($line)-4; $idx++) {
        /* Count how many of each character are in the code */
        $result = array_count_values(str_split(substr($line, $idx, 4)));
        /* Reverse sort the result so the highest count is in element 0 */
        arsort($result);
        /* If the highest count is 1 then all the characters are unique */
        if( array_values($result)[0] == 1 ){
            /* The marker is the current index + 4 */
            $marker = $idx+4;
            break;
        }
    }
    echo "Packet: " . $marker . "\n";
}
$file = null;

$file = new SplFileObject("day6input.txt", "r");
while( !$file->eof()) {
    $line = trim($file->fgets());
    /* Walk the string */
    $marker = 0;
    for( $idx = 0; $idx < strlen($line)-14; $idx++) {
        /* Count how many of each character are in the code */
        $result = array_count_values(str_split(substr($line, $idx, 14)));
        /* Reverse sort the result so the highest count is in element 0 */
        arsort($result);
        /* If the highest count is 1 then all the characters are unique */
        if( array_values($result)[0] == 1 ){
            /* The message is the current index + 14 */
            $marker = $idx+14;
            echo "Message: " . $marker . "\n";
            break;
        }
    }
}
