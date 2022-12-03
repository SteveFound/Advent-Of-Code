<?php
declare(strict_types = 1);

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
 * Abstract classes cannot be instantiated. All symbol classes will extend it and provide concrete impementations.
 * class: AbstractSymbol
 */
abstract class AbstractSymbol {
    /**
     * getSymbolScore : Return the score for playing this symbol
     * 
     * @return int
     */
    abstract function getSymbolScore(): int;

    /**
     * beats : Return the symbol that this symbol beats
     * 
     * @return AbstractSymbol
     */
    abstract function beats(): AbstractSymbol; 

    /**
     * draws : Return the symbol that this symbol draws with
     * 
     * @return AbstractSymbol
     */
    abstract function draws(): AbstractSymbol; 

    /**
     * losesTo : Return the symbol that this symbol loses to
     * 
     * @return AbstractSymbol
     */
    abstract function losesTo(): AbstractSymbol;

    /**
     * isA : Check if another symbol is the same symbol as this
     * @param AbstractSymbol $symbol
     * 
     * @return bool
     */
    abstract function isA( AbstractSymbol $symbol ): bool;
}

/*
 * Each of the symbols is both itself and an AbstractSymbol, so each can be treated as an AbstractSymbol without caring what it
 * actually is. All that matters is the objects implementation of the functions that define its behaviour
 */
class Rock extends AbstractSymbol {

    function getSymbolScore(): int { return 1; }

    function beats(): AbstractSymbol { return new Scissors(); }
    function draws(): AbstractSymbol { return new Rock(); }
    function losesTo(): AbstractSymbol { return new Paper(); }
    function isA(AbstractSymbol $obj): bool { return $obj instanceof Rock; }
}

class Paper extends AbstractSymbol {

    function getSymbolScore(): int { return 2; }

    function beats(): AbstractSymbol { return new Rock(); }
    function draws(): AbstractSymbol { return new Paper(); }
    function losesTo(): AbstractSymbol { return new Scissors(); }
    function isA(AbstractSymbol $obj): bool { return $obj instanceof Paper; }
}

class Scissors extends AbstractSymbol {

    function getSymbolScore(): int { return 3; }

    function beats(): AbstractSymbol { return new Paper(); }
    function draws(): AbstractSymbol { return new Scissors(); }
    function losesTo(): AbstractSymbol { return new Rock(); }
    function isA(AbstractSymbol $obj): bool { return $obj instanceof Scissors; }
}

/**
 * Class for concrete symbol instantition as required.
 */
class SymbolFactory {
    /**
     * buildSymbolFromCharacter : Convert the a symbol character into a symbol
     * @param string $symbolCharacter
     * 
     * @return AbstractSymbol
     */
    private function buildSymbolFromCharacter( string $symbolCharacter ): AbstractSymbol {
        switch($symbolCharacter) {
            case 'A':
            case 'X':
                return new Rock();
            case 'B':
            case 'Y':
                return new Paper();
            default:
                return new Scissors();
        }
    }

    /**
     * buildSymbolFromResult : Return the symbol which gives a known result from a round with a known elf symbol
     * if the result is X then the player must lose
     * if the result is Y then the player must draw
     * if the result is Z then the player must win
     * @param string $result
     * @param AbstractSymbol $elfSymbol
     * 
     * @return AbstractSymbol
     */
    private function buildSymbolFromResult( string $result, AbstractSymbol $elfSymbol ): AbstractSymbol {
        $builder = [ 'X' => $elfSymbol->beats(), 'Y' => $elfSymbol->draws(), 'Z' => $elfSymbol->losesTo() ];
        return $builder[$result];
    }

    /**
     * buildSymbols : Translates a line into an Elf and Player symbol.
     * 
     * Elf: A = Rock, B = Paper, C = Scissors
     * Player : X = Rock, Y = Paper, Z = Scissors
     *   
     * @param string $line      A string containing 2 characters (as above) separated by a space
     * 
     * @return array  Element[0] contains the Elf symbol, Element[1] contains the player symbol
     */
    public function buildSymbols($line): array
    {
        $symbols = array();

        // characters will become a 2 element array holding the 2 player characters
        $characters = explode(' ', trim($line));

        $symbols[0] = $this->buildSymbolFromCharacter($characters[0]);
        $symbols[1] = $this->buildSymbolFromCharacter($characters[1]);

        return $symbols;
    }

    /**
     * buildResult : Translates a line into an Elf and Player symbol.
     * 
     * Elf: A = Rock, B = Paper, C = Scissors
     * Result : X = Loss, Y = Draw, Z = Win so the player character is chosen to get that result given the elf character.
     *   
     * @param string $line      A string containing 2 characters (as above) separated by a space
     * 
     * @return array  Element[0] contains the Elf symbol, Element[1] contains the player symbol that gives the result
     */
    public function buildResult($line): array
    {
        $symbols = array();

        // characters will become a 2 element array holding the 2 player characters
        $characters = explode(' ', trim($line));

        $symbols[0] = $this->buildSymbolFromCharacter($characters[0]);
        $symbols[1] = $this->buildSymbolFromResult($characters[1], $symbols[0]);

        return $symbols;
    }

}

/**
 * scoreRound : Get the score from a round
 * @param array $symbols        = [ AbstractSymbol $elf, AbstractSymbol $player ];
 * 
 * @return int
 */
function scoreRound( array $symbols ): int {
    $elf = $symbols[0];
    $player = $symbols[1];
    $results = array( $player->beats(), $player->draws(), $player->losesTo() );
    $resultValue = array( MatchScore::Win->value, MatchScore::Draw->value, MatchScore::Loss->value );

    $score = $player->getSymbolScore();
    for( $idx = 0; $idx < 3; $idx++) {
        if( $elf->isA( $results[$idx] )) {
            $score += $resultValue[$idx];
        }
    }
    return $score;
}


$builder = new SymbolFactory();
$file = new SplFileObject("rps.txt", "r");
$totalPart1 = 0;
$totalPart2 = 0;
while( !$file->eof()) {
    $line = trim($file->fgets());
    if( strlen($line) > 0) {
        $totalPart1 += scoreRound($builder->buildSymbols($line));
        $totalPart2 += scoreRound($builder->buildResult($line));
    }
}
echo "Part 1\n";
echo "Total Score: " . $totalPart1 . "\n";
echo "\nPart 2\n";
echo "Total Score: " . $totalPart2 . "\n";
?>