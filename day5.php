<?php
declare(strict_types = 1);

/* Define a stack */
class Stack {
    private SplStack $stack;

    /**
     * __construct : A stack is created by a string of characters that define a crate in order bottom to top.
     * @param string $characters
     * 
     * @return void
     */
    public function __construct( string $characters ) {
        $this->stack = new SplStack();
        foreach (str_split($characters) as $value) {
            $this->stack->push($value);
        }
    }

    /**
     * grabCrate : Grab the top crate of a stack.
     * 
     * @return string
     */
    public function grabCrate(): string {
        return (string)$this->stack->pop();
    }

    /**
     * dropCrate : Drop a crate onto a stack
     * @param string $crate
     * 
     * @return void
     */
    public function dropCrate(string $crate): void {
        $this->stack->push($crate);
    }

    /**
     * topCrate : Return the crate on the top of the stack without removing it.
     * 
     * @return string
     */
    public function topCrate(): string {
        if( $this->stack->count() > 0 ) {
            return (string)$this->stack->top();
        } else {
            return ' ';
        }
    }

    /**
     * moveTo : Grab a crate from this stack then drop it onto another.
     * @param Stack $that
     * 
     * @return void
     */
    public function moveTo( Stack $that ) {
        $that->dropCrate($this->grabCrate());
    }
}

/**
 * Class to manage several stacks and move crates between them as instructed.
 * class: StackManager
 */
class StackManager {
    private array $stacks;

    /**
     * __construct : Take an array of strings that define each stack and create an array of stacks from it.
     * @param array $stackStrings
     * 
     * @return void
     */
    public function __construct(array $stackStrings) {
        //One stack per string
        $stacks = array(count($stackStrings));
        $idx = 0;
        foreach($stackStrings as $crates) {
            $this->stacks[$idx] = new Stack($crates);
            $idx++;
        }
    }

    /**
     * doMove : Move a number of crates from one stack to another, one at a time.
     * @param Stack $from
     * @param Stack $to
     * @param int $count
     * 
     * @return void
     */
    private function doMove( Stack $from, Stack $to, int $count ): void {
        for( $idx = 0; $idx < $count; $idx++ ){
            $from->moveTo($to);
        }
    }

    /**
     * move9000 : Parse a command that defines the number of crates to move from one to the other and move them.
     * @param string $command
     * 
     * @return void
     */
    public function move9000(string $command): void {
        // Format of command is 'move 2 from 2 to 7'
        list($count,$from,$to) = sscanf($command, "move %d from %d to %d");
        $this->doMove($this->stacks[$from-1], $this->stacks[$to-1], $count);
    }

    /**
     * move9001 : Parse a command that defines the number of crates to move from one to the other and move them.
     * The difference from move9000 is that the crates must be added in the order they were on the original stack.
     * To achieve this, they are moved from the source stack to an empty temporary stack in reverse order then moved from
     * the temporary stack to the destination stack in reverse order.  
     * @param string $command
     * 
     * @return void
     */
    public function move9001(string $command): void {
        // Format of command is 'move 2 from 2 to 7'
        $tmpStack = new Stack('');
        list($count,$from,$to) = sscanf($command, "move %d from %d to %d");
        $this->doMove($this->stacks[$from-1], $tmpStack, $count);
        $this->doMove($tmpStack, $this->stacks[$to-1], $count);
    }

    /**
     * topCrates : Concatenate the top crates into a string.
     * 
     * @return string
     */
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
