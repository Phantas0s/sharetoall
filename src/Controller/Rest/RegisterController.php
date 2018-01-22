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
    protected $modelName = 'User';

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
        $this->model = $this->createModel($this->modelName);
    }

    protected function createModel(string $name)
    {
        return $this->modelFactory->create($name);
    }

    public function postAction(Request $request): User
    {
        $dataForm = $request->request->get('form');
        $form = $this->formFactory->create('User\Register');

        $this->verifyUserExists($dataForm['userEmail']);

        $form->setDefinedWritableValues($dataForm)->validate();

        if ($form->hasErrors()) {
            throw new FormInvalidException($form->getFirstError());
        }

        $dataForm = $form->getValuesByTag('user');
        $dataForm['userVerifEmailToken'] = $form->getVerificationToken();
        $dataForm['userPassword'] = $form->getPasswordHash();

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
            throw new FormInvalidException('This email address already exists!');
        }
    }
}
