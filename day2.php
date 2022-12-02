<?php
declare(strict_types = 1);

/**
 * Possible symbols
 */
enum Symbol
{
    case Rock;
    case Paper;
    case Scissors;
}

/**
 * Scores for playing a symbol
 */
enum SymbolScore: int
{
    case Rock = 1;
    case Paper = 2;
    case Scissors = 3;
}

/**
 * Match scores
 */
enum MatchScore: int
{
    case Loss = 0;
    case Draw = 3;
    case Win = 6;
}


/**
 * scoreRound : Determine the player score for a round. 
 *     Rock > Scissors, Scissors > Paper, Paper > Rock
 *     Player scores Rock = 1, Paper = 2, Scissors = 3
 *     Player scores Loss = 0, Draw = 3, Win = 6
 * @param Symbol $elf       Elf symbol
 * @param Symbol $player    Player symbol
 * 
 * @return int
 */
function scoreRound( Symbol $elf, Symbol $player): int
{
    $score = 0;
    switch ($player) {
        case Symbol::Rock:
            // Add score for playing symbol
            $score += SymbolScore::Rock->value;
            // Add score for win, draw or loss
            if ($elf == Symbol::Rock) {
                $score += MatchScore::Draw->value; 
            } else {
                $score += ($elf == Symbol::Scissors) ? MatchScore::Win->value : MatchScore::Loss->value;
            } 
            break;
        case Symbol::Paper:
            // Add score for playing symbol
            $score += SymbolScore::Paper->value;
            // Add score for win, draw or loss
            if ($elf == Symbol::Paper) {
                $score += MatchScore::Draw->value; 
            } else {
                $score += ($elf == Symbol::Rock) ? MatchScore::Win->value : MatchScore::Loss->value;
            } 
            break;
        default:                                    // Must be scissors       
            // Add score for playing symbol
            $score += SymbolScore::Scissors->value;
            // Add score for win, draw or loss
            if ($elf == Symbol::Scissors) {
                $score += MatchScore::Draw->value; 
            } else {
                $score += ($elf == Symbol::Paper) ? MatchScore::Win->value : MatchScore::Loss->value;
            } 
            break;
    }
    return $score;
}

/**
 * forceResult : Choose a player symbol to force a result against a known elf symbol
 * @param Symbol $elf       Rock, Paper or Scissors
 * @param string $result    X = Lose, Y = Draw, Z = Win
 * 
 * @return Symbol           The player symbol that will give the result.
 */
function forceResult( Symbol $elf, string $result): Symbol
{
    // Assume draw
    $player = $elf;
    switch( $result ) {
        //Lose
        case 'X':
            if( $elf == Symbol::Rock) {
                $player = Symbol::Scissors;
            } else  if ( $elf == Symbol::Paper ){
                $player = Symbol::Rock;
            } else {
                $player = Symbol::Paper;
            }
            break;
        //Win
        case 'Z':
            if( $elf == Symbol::Rock) {
                $player = Symbol::Paper;
            } else  if ( $elf == Symbol::Paper ){
                $player = Symbol::Scissors;
            } else {
                $player = Symbol::Rock;
            }
            break;
        // draw
        default:
            $player = $elf;
            break;
    }
    return $player;
}

/**
 * getSymbols : Translates a line into an Elf and Player symbol.
 * 
 * if $mode == true then
 * Elf: A = Rock, B = Paper, C = Scissors
 * Player : X = Rock, Y = Paper, Z = Scissors
 * 
 * if $mode == false
 * Elf: A = Rock, B = Paper, C = Scissors
 * Result : X = Loss, Y = Draw, Z = Win so the player character is chosen to get that result given the elf character.
 *   
 * @param string $line      A string containing 2 characters (as above) separated by a space
 * 
 * @return array  Element[0] contains the Elf symbol, Element[1] contains the player symbol
 */
function getSymbols($line, $mode): array
{
    $symbols = array();

    // characters will become a 2 element array holding the 2 player characters
    $characters = explode(' ', trim($line));

    // Convert the elf character to an enum
    switch ($characters[0]) {
        case 'A':
            $symbols[0] = Symbol::Rock;
            break;
        case 'B':
            $symbols[0] = Symbol::Paper;
            break;
        case 'C':
            $symbols[0] = Symbol::Scissors;
            break;
        default:
            die( "Unexpected elf character: " . $characters[0]);
    }

    if( $mode ) {
        // Convert the player character to an enum
        switch ($characters[1]) {
            case 'X':
                $symbols[1] = Symbol::Rock;
                break;
            case 'Y':
                $symbols[1] = Symbol::Paper;
                break;
            case 'Z':
                $symbols[1] = Symbol::Scissors;
                break;
            default:
                die( "Unexpected player character: " . $characters[1]);
        }
    } else {
        $symbols[1] = forceResult( $symbols[0], $characters[1]);
    }

    return $symbols;
}



function showRound( Symbol $elf, Symbol $player, $score): void
{
    switch($elf) {
        case Symbol::Rock:
            echo "Rock";
            break;
        case Symbol::Paper:
            echo "Paper";
            break;
        case Symbol::Scissors:
            echo "Scissors";
            break;
        default:
            echo "???";
    }
    echo " V ";
    switch($player) {
        case Symbol::Rock:
            echo "Rock";
            break;
        case Symbol::Paper:
            echo "Paper";
            break;
        case Symbol::Scissors:
            echo "Scissors";
            break;
        default:
            echo "???";
    }
    echo " Score: " . $score . "\n";
}

$file = new SplFileObject("rps.txt", "r");
$totalPart1 = 0;
$totalPart2 = 0;
while( !$file->eof()) {
    $line = trim($file->fgets());
    if( strlen($line) > 0) {
        $symbols = getSymbols($line, true);
        $score = scoreRound($symbols[0], $symbols[1]);
        $totalPart1 += $score;

        $symbols = getSymbols($line, false);
        $score = scoreRound($symbols[0], $symbols[1]);
        showRound($symbols[0], $symbols[1], $score);
        $totalPart2 += $score;
    }
}
echo "Part 1\n";
echo "Total Score: " . $totalPart1 . "\n";
echo "\nPart 2\n";
echo "Total Score: " . $totalPart2 . "\n";
?>