<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

final class SignPresenter extends Presenter{

    // zasílání údajů do šablony
    public function renderIn(){
        $this->template->status = 'SIGN IN PAGE';
    }

    public function actionOut(){
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení bylo úspěšné');
        $this->redirect('Sign:in');
    }


    protected function createComponentSignInForm(){
        $form = new Form();
        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplntě uživatelské jméno');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplntě heslo');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this,'signInFormSucceded'];

        return $form;
    }

    public function signInFormSucceded(Form $form, \stdClass $values){
        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Player:default');

        } catch (AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }
}