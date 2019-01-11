<?php
/**
 * Created by PhpStorm.
 * User: Programovani
 * Date: 2.1.2019
 * Time: 16:04
 */

namespace App\Presenters;

use Nette\Application\UI\Presenter; // for extends Presenter , z čeho dědí

use App\Model\CalculatorManager; // CalculatorManager for model

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Presenter kalkulačky.
 * @package App\Presenters
 */
class CalculatorPresenter extends Presenter
{
    /** @var int|null výsledek operace nebo null, default is null - ATTRIBUTE */
    private $result = null;

    /** @var CalculatorManager Instance třídy modelu pro práci s operacemi kalkulačky. */
    private $calculatorManager;

    /**
     * Konstruktor s injektovaným modelem pro práci s operacemi kalkulačky.
     * @param CalculatorManager $calculatorManager automaticky injektovaná třída modelu pro práci s operacemi kalkulačky
     */
    public function __construct(CalculatorManager $calculatorManager){
        parent::__construct();
        $this->calculatorManager = $calculatorManager;


    }

    /** Výchozí vykreslovací metoda tohoto presenteru. - nemá být výchzí _construct ? */
    public function renderDefault()
    {
        // Předání výsledku do šablony.
        $this->template->result = $this->result;
    }




    /** Definice konstant pro zprávy formuláře. */
    const
        FORM_MSG_REQUIRED = 'Tohle pole je povinné.',
        FORM_MSG_RULE = 'Tohle pole má neplatný formát.';

    /**
     * Vrátí formulář kalkulačky.
     * @return Form formulář kalkulačky
     */
    protected function createComponentCalculatorForm()
    {
        $form = new Form;
        // Získáme existující operace kalkulačky a dáme je do výběru operací.
        $form->addRadioList('operation', 'Operace:', $this->calculatorManager->getOperations())
            ->setDefaultValue(CalculatorManager::ADD)
            ->setRequired(self::FORM_MSG_REQUIRED);
        $form->addText('x', 'První číslo:')
            ->setType('number')
            ->setDefaultValue(0)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);
        $form->addText('y', 'Druhé číslo:')
            ->setType('number')
            ->setDefaultValue(0)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE)
            // Ošetříme dělení nulou.
            ->addConditionOn($form['operation'], Form::EQUAL, CalculatorManager::DIVIDE)
            ->addRule(Form::PATTERN, 'Nelze dělit nulou.', '^[^0].*');
        $form->addSubmit('calculate', 'Spočítej výsledek');
        $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];
        return $form;
    }

    /**
     * Funkce se vykonaná při úspěšném odeslání formuláře kalkulačky a zpracuje odeslané hodnoty.
     * @param Form $form        formulář kalkulačky
     * @param ArrayHash $values odeslané hodnoty formuláře
     */
    public function calculatorFormSucceeded($form, $values)
    {
        // Necháme si vypočítat výsledek podle zvolené operace a zadaných hodnot.
        $this->result = $this->calculatorManager->calculate($values->operation, $values->x, $values->y);
    }
}