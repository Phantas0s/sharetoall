<?php declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\FormInvalidException;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Model\User;
use App\Service\Mail;
use App\Service\Session;
use InputValidation\Form;
use Symfony\Component\HttpFoundation\Request;

class RegisterController
{
    protected $session;
    protected $modelFactory;
    protected $formFactory;
    protected $model;

    /** @var Mail */
    private $mail;

    public function __construct(
        Session $session,
        ModelFactory $modelFactory,
        FormFactory $formFactory,
        Mail $mail
    ) {
        $this->session = $session;
        $this->modelFactory = $modelFactory;
        $this->formFactory = $formFactory;
        $this->mail = $mail;

        $this->model = $this->createModel("User");
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
