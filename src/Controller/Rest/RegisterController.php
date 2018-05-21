<?php declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\FormInvalidException;
use App\Form\FormFactory;
use App\Model\Newsletter;
use App\Model\User;
use App\Service\Mail;
use App\Service\Session;
use InputValidation\Form;
use Symfony\Component\HttpFoundation\Request;

class RegisterController
{
    protected $session;
    protected $formFactory;
    protected $model;

    /** @var Mail */
    private $mail;

    /** @var Newsletter */
    private $newsletterModel;

    public function __construct(
        Session $session,
        User $user,
        Newsletter $newsletter,
        FormFactory $formFactory,
        Mail $mail
    ) {
        $this->session = $session;
        $this->formFactory = $formFactory;
        $this->mail = $mail;

        $this->model = $user;
        $this->newsletterModel = $newsletter;
    }

    protected function createModel(string $name)
    {
        return $this->modelFactory->create($name);
    }

    public function postAction(Request $request): User
    {
        $form = $this->formFactory->create('User\Register');

        $form->setDefinedWritableValues($request->request->get("form"))->validate();
        $dataForm = $form->getValuesByTag('user');
        $this->verifyUserExists($dataForm['userEmail']);

        if ($form->hasErrors()) {
            throw new FormInvalidException($form->getFirstError());
        }

        $this->model->save($dataForm);

        if ($dataForm['userNewsletter']) {
            $this->newsletterModel->save(['newsletterEmail' => $dataForm['userEmail']]);
        }

        $this->mail->confirmEmail(
            $this->model
        );

        return $this->model;
    }

    private function verifyUserExists(string $userEmail)
    {
        $users = $this->model->findAll(['userEmail' => $userEmail]);

        if (!empty($users)) {
            throw new FormInvalidException('The email address already exists. Please choose a different one.');
        }
    }
}
