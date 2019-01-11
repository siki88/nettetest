<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter; // for extends Presenter , z čeho dědí
use Nette\Database\Context;
use Nette;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use App\Model\UploadFileModel;


final class PlayerPresenter extends Presenter{

    private $database;

    /**
     * PlayerPresenter constructor.
     */
    public function __construct(Context $database){
        //Předání databázového spojení:
        $this->database = $database;
    }

    public function renderDefault(){

        /* Načítání příspěvků z databáze:
         * Presenter nyní obsahuje jednu renderovací metodu renderDefault(), která předává data z databáze do šablony (View).
         * Šablony jsou umístěny v app/presenters/templates/{PresenterName}/{viewName}.latte,
         * takže v tomto případě je šablona umístěna v app/presenters/templates/Homepage/default.latte.
         * V šabloně nyní bude k dispozici proměnná $posts, ve které jsou příspěvky získané z databáze.
         */
        $this->template->posts = $this->database->table('songs')->where('users_id','1');
        $this->template->status = 'PROJECT IS EXAMPLE';
    }

    public function actionDefault(){
        $this->controlUserLogin();
    }

    //render slouží k zasílání údajů do templaet
    public function renderAdd(){
        //Pro třeba získání dat z databaze a předání je do šablony;
        //var_dump('renderAdd');
    }

    public function actionAdd(){
        $this->controlUserLogin();
    }

    private function controlUserLogin(){
        if(!$this->getUser()->isLoggedIn()){
            $this->redirect('Sign:in');
        }
    }

    //CREATE FORM COMPONENT - NA VLOZENI SONGU
    protected function createComponentUploadForm(){

        $form = new Form; // means Nette\Application\UI\Form
/*
        $form->addUpload('upload', 'Song')
            ->setRequired(true) // nepovinný
            ->addCondition(Form::FILLED)
            ->addRule(Form::MIME_TYPE, 'Song must be MP3.',['audio/mp3']);
            ->addRule(Form::MAX_FILE_SIZE, 'Max size of file is 10 mB.', 10 * 1024 * 1024);
*/
        $form->addText('name', 'Jméno:')
            ->setRequired(true);

        $form->addSubmit('send', 'Send song');

        //callback metodu, která uloží odeslaná data.
        //znamená „po úspěšném odeslání formuláře zavolej metodu commentFormSucceeded ze současného presenteru“
        $form->onSuccess[] = [$this, 'uploadFormSucceeded'];

        return $form;
    }

    //SEND FORM FOR SAVE DB
    public function uploadFormSucceeded(Form $form, \stdClass $values){

        if(!$this->getUser()->isLoggedIn()){
            $this->error('Pro vytvoření, nebo editování příspěvku se musíte přihlásit.');
        }

        /*
         * tato metoda získá data z formuláře,
         * vloží je do databáze,
         * vytvoří zprávu pro uživatele o úspěšném uložení příspěvku a přesměruje na stránku s novým příspěvkem
         */
        //save form and back song id
        $post = $this->database->table('songs')->insert($values);

        //zašle upozornění
        $this->flashMessage('Song vložen','success');

        // Redirect metoda doslova přesměrovává zpět na aktuální stránku. - logicky
        $this->redirect('SongInfo:show', $post->id);
    }
}
