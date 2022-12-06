<?php
declare(strict_types = 1);

class Cell {
    private bool $state = false;         /* true = on, false = off */

    public function __construct()   { $this->turnOff(); }
    public function turnOn(): void  { $this->state = true; }
    public function turnOff(): void { $this->state = false; }
    public function toggle(): void  { $this->state = !$this->state; }
    public function isOn(): bool    { return $this->state; }
    public function isOff(): bool   { return !$this->state; }
}

class Grid {
    private array $grid;

    // Create a 2D array of 1000000 cells.
    // The array is a 1000 element array with each element holding another 1000 element array.
    public function __construct()
    {
        $this->grid = array(1000);
        for( $row = 0; $row < 1000; $row++ ){
            $arr = array(1000);
            for( $col = 0; $col < 1000; $col++ ) {
                $arr[$col] = new Cell();
            }
            $this->grid[$row] = $arr;
        }      
    }

    public function turnOn( int $row1, int $col1, int $row2, int $col2 ): void {
        for( $row = $row1; $row <= $row2; $row++ ){
            for( $col = $col1; $col <= $col2; $col++ ) {
                $this->grid[$row][$col]->turnOn();
            }
        }
    }

    public function turnOff( int $row1, int $col1, int $row2, int $col2 ): void {
        for( $row = $row1; $row <= $row2; $row++ ){
            for( $col = $col1; $col <= $col2; $col++ ) {
                $this->grid[$row][$col]->turnOff();
            }
        }
    }

    public function toggle( int $row1, int $col1, int $row2, int $col2 ): void {
        for( $row = $row1; $row <= $row2; $row++ ){
            for( $col = $col1; $col <= $col2; $col++ ) {
                $this->grid[$row][$col]->toggle();
            }
        }
    }

    public function countOn(): int {
        $count = 0;
        for( $row = 0; $row < 1000; $row++ ){
            for( $col = 0; $col < 1000; $col++ ) {
                $count += $this->grid[$row][$col]->isOn() ? 1 : 0;
            }
        }
        return $count;
    }

    public function countOff(): int {
        $count = 0;
        for( $row = 0; $row < 1000; $row++ ){
            for( $col = 0; $col < 1000; $col++ ) {
                $count += $this->grid[$row][$col]->isOff() ? 1 : 0;
            }
        }
        return $count;
    }
}

$grid = new Grid();
$grid->turnOn( 0,0, 999,999);           
echo $grid->countOn() . " On\n";        // Outputs 1000000
$grid->toggle( 0,0, 999,0 );            
echo $grid->countOn() . " On\n";        // Outputs 999000
$grid->turnOff( 499,499, 500,500 );     
echo $grid->countOn() . " On\n";        // Outputs 998996

?>