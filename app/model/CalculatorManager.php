<?php
/**
 * Created by PhpStorm.
 * User: Programovani
 * Date: 2.1.2019
 * Time: 14:54
 */

namespace App\Model;

use Nette\SmartObject;

class CalculatorManager {

    use SmartObject;

            const ADD = 1,
                  SUBTRACT = 2,
                  MULTIPLY = 3,
                  DIVIDE = 4;

    public function add($x,$y){
        return($x + $y);
    }

    public function subtrack($x,$y){
        return($x - $y);
    }

    public function multiply($x,$y){
        return($x * $y);
    }

    public function divide($x,$y){
        return round($x / $y);
    }

    //tu přiřazuješ do pole constanty a těm přiřazuješ operaci
    public function getOperations(){
        return array(self::ADD => 'Sčítání',
                     self::SUBTRACT => 'Odčítání',
                     self::MULTIPLY => 'Násobení',
                     self::DIVIDE => 'Dělení'
                    );
    }

    // sem posíláš v $operation typ operace, a dle toho voláš metody
    public function calculate($operation, $x, $y){
        switch($operation){
            case self::ADD:
                return $this->add($x,$y);
            case self::SUBTRACT:
                return $this->subtract($x,$y);
            case self::MULTIPLY:
                return $this->multiply($x,$y);
            case self:DIVIDE:
                return $this->divide($x,$y);
            default:
                return null;
        }
    }


}