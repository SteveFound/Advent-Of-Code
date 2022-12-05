<?php
declare(strict_types = 1);

/* Define a stack */

class Stack {
    private SplStack $stack;

    // Fill a stack with characters
    public function __construct( string $characters ) {
        $this->stack = new SplStack();
        foreach (str_split($characters) as $value) {
            $this->stack->push($value);
        }
    }

    // Grab top most crate
    public function grabCrate(): string {
        return (string)$this->stack->pop();
    }

    // Drop a crate onto this stack
    public function dropCrate(string $crate): void {
        $this->stack->push($crate);
    }
 
    // Look at the top crate
    public function topCrate(): string {
        if( $this->stack->count() > 0 ) {
            return (string)$this->stack->top();
        } else {
            return ' ';
        }
    }

    // Move a character from this stack to another stack
    public function moveTo( Stack $that ) {
        $that->dropCrate($this->grabCrate());
    }
}

class StackManager {
    private array $stacks;

    // Array of stack strings
    public function __construct(array $stackStrings) {
        //One stack per string
        $stacks = array(count($stackStrings));
        $idx = 0;
        foreach($stackStrings as $crates) {
            $this->stacks[$idx] = new Stack($crates);
            $idx++;
        }
    }

    private function doMove( Stack $from, Stack $to, int $count ): void {
        for( $idx = 0; $idx < $count; $idx++ ){
            $from->moveTo($to);
        }
    }

    public function move9000(string $command): void {
        // Format of command is 'move 2 from 2 to 7'
        list($count,$from,$to) = sscanf($command, "move %d from %d to %d");
        $this->doMove($this->stacks[$from-1], $this->stacks[$to-1], $count);
    }

    public function move9001(string $command): void {
        // Format of command is 'move 2 from 2 to 7'
        $tmpStack = new Stack('');
        list($count,$from,$to) = sscanf($command, "move %d from %d to %d");
        $this->doMove($this->stacks[$from-1], $tmpStack, $count);
        $this->doMove($tmpStack, $this->stacks[$to-1], $count);
    }

    public function topCrates(): string {
        $output = '';
        foreach($this->stacks as $stack) {
            $output .= $stack->topCrate();
        }
        return $output;
    } 
}

/*
                [V]     [C]     [M]
[V]     [J]     [N]     [H]     [V]
[R] [F] [N]     [W]     [Z]     [N]
[H] [R] [D]     [Q] [M] [L]     [B]
[B] [C] [H] [V] [R] [C] [G]     [R]
[G] [G] [F] [S] [D] [H] [B] [R] [S]
[D] [N] [S] [D] [H] [G] [J] [J] [G]
[W] [J] [L] [J] [S] [P] [F] [S] [L]
 1   2   3   4   5   6   7   8   9 
*/

/* Part 1 */
$stackManager = new StackManager(['WDGBHRV', 'JNGCRF', 'LSFHDNJ', 'JDSV', 'SHDRQWNV', 'PGHCM', 'FJBGLZHC', 'SJR', 'LGSRBNVM']);
$file = new SplFileObject("day5input.txt", "r");
while( !$file->eof()) {
    $line = trim($file->fgets());
    if( strlen($line)  > 0 ) {
        $stackManager->move9000($line);
    }
}
$file = null;
echo "Part 1: " . $stackManager->topCrates() . "\n";

/* Part 2 */
$stackManager = new StackManager(['WDGBHRV', 'JNGCRF', 'LSFHDNJ', 'JDSV', 'SHDRQWNV', 'PGHCM', 'FJBGLZHC', 'SJR', 'LGSRBNVM']);
$file = new SplFileObject("day5input.txt", "r");
while( !$file->eof()) {
    $line = trim($file->fgets());
    if( strlen($line)  > 0 ) {
        $stackManager->move9001($line);
    }
}
echo "Part 1: " . $stackManager->topCrates() . "\n";
