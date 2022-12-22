#include <iostream>
#include <fstream>
#include <string>
#include <map>


using namespace std;

enum class SymbolType { Rock, Paper, Scissors };

/**
 * Base class for symbols
*/
class Symbol
{
    /**
     * Virtual functions that the concrete symbols override
    */
    protected:
    // Type ID
    virtual SymbolType getType() = 0;
    // The score for playing the symbol
    virtual int playScore() = 0;
    public:
    // Which symbol this symbol beats
    virtual SymbolType beats() = 0;
    // Which symbol this symbol draws with
    virtual SymbolType drawsWith() = 0;
    // Which symbol this symbol loses to
    virtual SymbolType losesTo() = 0;

    /**
     * Play a round against an opponent
    */
    int scoreRound( Symbol *opponent ){
        int score = playScore();
        if( *this > *opponent ) {
            score += 6;
        } else if( *this == *opponent ){
            score += 3;
        }
        return score;
    }

    /**
     * Draw
    */
    bool operator==(Symbol &rhs) {
        return( drawsWith() == rhs.getType() );
    }

    /**
     * This symbol wins 
    */
    bool operator>(Symbol &rhs) {
        return( beats() == rhs.getType() );
    }

    /**
     * This symbol loses
    */
    bool operator<(Symbol &rhs) {
        return( losesTo() == rhs.getType() );
    }
};

/**
 * Rock Symbol
*/
class Rock : public Symbol
{
    protected:
    SymbolType getType() { return SymbolType::Rock; }
    int playScore() { return 1;}
    public:
    SymbolType beats() { return SymbolType::Scissors; }
    SymbolType drawsWith() { return SymbolType::Rock; }
    SymbolType losesTo() { return SymbolType::Paper; }
};

/**
 * Paper Symbol
*/
class Paper : public Symbol
{
    protected:
    SymbolType getType() { return SymbolType::Paper; }
    int playScore() { return 2;}
    public:
    SymbolType beats() { return SymbolType::Rock; }
    SymbolType drawsWith() { return SymbolType::Paper; }
    SymbolType losesTo() { return SymbolType::Scissors; }
};

/**
 * Scissors symbol
*/
class Scissors : public Symbol
{
    protected:
    SymbolType getType() { return SymbolType::Scissors; }
    int playScore() { return 3;}
    public:
    SymbolType beats() { return SymbolType::Paper; }
    SymbolType drawsWith() { return SymbolType::Scissors; }
    SymbolType losesTo() { return SymbolType::Rock; }
};

/**
 * Create symbol classes
*/
class SymbolFactory
{
    private:
    map<int, Symbol *> symbols;
    
    public:
    /**
     * Build the symbol lookup table
    */
    SymbolFactory() {
        symbols[static_cast<int>(SymbolType::Rock)] = new Rock();
        symbols['A'] = new Rock();
        symbols['X'] = new Rock();
        symbols[static_cast<int>(SymbolType::Paper)] = new Paper();
        symbols['B'] = new Paper();
        symbols['Y'] = new Paper();
        symbols[static_cast<int>(SymbolType::Scissors)] = new Scissors();
        symbols['C'] = new Scissors();
        symbols['Z'] = new Scissors();
    }

    /**
     * Delete all the objects in the lookup table
    */
    ~SymbolFactory() {
         map<int, Symbol *>::iterator it = symbols.begin();
         while( it != symbols.end()) {
            delete it->second;
            it++;
         }
    }

    /**
     * Turn a player/elf character into a symbol
    */
    Symbol *getSymbol( int symChar) {
        return symbols[symChar]; 
    }

    /**
     * Turn a symbol type into a symbol
    */
    Symbol *getSymbol( SymbolType type ) {
        return symbols[static_cast<int>(type)];
    }

    /**
     * Get the player symbol that gives a result
    */
    Symbol *forceSymbol( Symbol *opponent, int symChar ) {
        switch( symChar ){
            // player loses
            case 'X':
                return getSymbol(opponent->beats());
            // player draws
            case 'Y':
                return getSymbol(opponent->drawsWith());
            // player wins
            case 'Z':
                return getSymbol(opponent->losesTo());
            default:
                cout << "Unexpected result character: \'" << (char)symChar << "\'";
                throw invalid_argument("Unexpected Character");
        }
    }
};


/**
 * scoreRound : Determine the player score for a round. 
 */
int scoreRound( char elfChar, char plrChar, bool mode, SymbolFactory *factory )
{
    // The symbol pointers are managed by the factory so there is no need to delete them
    Symbol *elf = factory->getSymbol((int)elfChar);
    if(mode){
        return factory->getSymbol((int)plrChar)->scoreRound(elf);
    } else {
        return factory->forceSymbol(elf, (int)plrChar)->scoreRound(elf);
    }
}

/**
 * Let's do this
*/
int main() {
    int totalPart1 = 0;
    int totalPart2 = 0;

    ifstream rps("rps.txt");
    SymbolFactory *factory = new SymbolFactory();
    string line;
    while( getline(rps, line)) {
        if( line.length() > 1) {
            totalPart1 += scoreRound(line[0], line[2], true, factory);
            totalPart2 += scoreRound(line[0], line[2], false, factory);
        }
    }
    rps.close();

    cout << "Part 1: Total Score: " << totalPart1 << endl;
    cout << "Part 2: Total Score: " << totalPart2 << endl;

    delete factory;
}