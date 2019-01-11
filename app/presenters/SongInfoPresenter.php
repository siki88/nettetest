<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter; // for extends Presenter , z čeho dědí
use Nette\Database\Context;
use Nette;
use Nette\Application\UI\Form;


final class SongInfoPresenter extends Presenter{

    private $database;

    /**
     * PlayerPresenter constructor.
     */
    public function __construct(Context $database){
        //Předání databázového spojení:
        $this->database = $database;
    }

    // Render metody se používají pro vložení dat do šablon.
    public function renderShow($postId){

        //vytáhne song podle id
        $post = $this->database->table('songs')->get($postId);

        if(!$post){
            $this->error('skladba nenalezena');
        }

        //pošle údaje vytaženého songu do template
        $this->template->post = $post;
        //vytáhne komentáře vztahujícímu se k songu
        $this->template->comments = $post->related('comment')->order('created_at');

        $this->template->important = 'Dobrý den!';
    }

    /*
     * action metody toho mohou dělat mnohem více, měly by kontrolovat,
     * jestli požadovaná stránka může být zobrazena současným návštěvníkem,
     * nebo uživatelem a mohou také vykonávat spoustu práce, která nenáleží callbackům.
     */
    public function actionEdit($postId)
    {
        $comment = $this->database->table('comments')->get($postId);
        if (!$comment) {
            $this->error('Příspěvek nebyl nalezen');
        }

        $this['commentForm']->setDefaults($comment->toArray());
    }

    //CREATE FORM COMPONENT
    protected function createComponentCommentForm(){

        $form = new Form; // means Nette\Application\UI\Form

        $form->addText('name', 'Jméno:')
            ->setRequired();

        $form->addEmail('email', 'Email:');

        $form->addTextArea('content', 'Komentář:')
            ->setRequired();

        $form->addSubmit('send', 'Publikovat komentář');

        //callback metodu, která uloží odeslaná data.
        //znamená „po úspěšném odeslání formuláře zavolej metodu commentFormSucceeded ze současného presenteru“
        $form->onSuccess[] = [$this, 'commentFormSucceeded'];

        return $form;
    }

    //SEND FORM FOR SAVE DB
    public function commentFormSucceeded(Form $form, \stdClass $values){

        if(!$this->getUser()->isLoggedIn()){
            $this->error('Pro vytvoření, nebo editování příspěvku se musíte přihlásit.');
        }

        //vytáhneš id z getu stránky
        $postId = $this->getParameter('postId');
        $controlPost = $this->database->table('comments')->where('id',$postId)->count();

        if ($controlPost === 1) {
            //update form
            $post = $this->database->table('comments')->get($postId);
            $post->update($values);
        } else {
            //save form
            $post = $this->database->table('comments')->insert([
                'songs_id' => $postId,
                'name'     => $values->name,
                'email'    => $values->email,
                'content'  => $values->content
                ]);
        }

        $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $this->redirect('show', $post->songs_id);

    }
}
